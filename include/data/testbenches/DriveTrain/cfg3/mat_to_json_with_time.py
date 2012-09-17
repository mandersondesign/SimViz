from scipy.io import loadmat
import numpy as np
import json
import os
import sys

def load_mat(datafile, expand_param_data=True):
    data = loadmat(datafile, matlab_compatible=True)
    
    names = data['name'].transpose()
    descrips = data['description'].transpose()
    
    data_loc = data['dataInfo'][0]
    data_sign = np.sign(data['dataInfo'][1])
    data_col = np.abs(data['dataInfo'][1]) - 1
    
    num_time_pts = data['data_2'][0].shape[0]
    
    data_dict = {}
    desc_dict = {}
    
    for i in xrange(names.shape[0]):
        
        name = ''.join([str(e) for e in names[i]]).rstrip()
        
        if name == 'Time':
            name = 'time'
        
        descrip = ''.join([str(e) for e in descrips[i]]).rstrip()
        
        desc_dict[name] = descrip
        
        if data_loc[i] == 1:
            if expand_param_data:
                data_dict[name] = (np.ones(num_time_pts) * 
                                   data['data_1'][data_col[i]][0] * data_sign[i])
            else:
                data_dict[name] = data['data_1'][data_col[i]] * data_sign[i]
        else:
            data_dict[name] = data['data_2'][data_col[i]] * data_sign[i]
    
    return data_dict, desc_dict


def getNode(name, tree_structure, data_link):
    node = {}
    
    pos = name.find('.')
    if (pos == -1):
        node['data_link'] = data_link # same for now
        node['name'] = name
        node['children'] = []
        tree_structure.append(node)
    else:
        node['name'] = name[:pos]
        
        needToAppend = True

        for node_item in tree_structure:
            if (node_item['name'] == node['name']):
                node = node_item
                needToAppend = False
                
        if not node.has_key('children'):
            node['children'] = []
            
        if needToAppend:
            tree_structure.append(node)

        getNode(name[pos+1:], node['children'], data_link)
    
    
    
if __name__ == '__main__':
    if (len(sys.argv) > 1):
        if not os.path.exists(sys.argv[1]):
            print 'Given result file does not exist: {0}'.format(sys.argv[1])
            os.exit(0)
        
        # file names
        base_name = os.path.splitext(os.path.basename(sys.argv[1]))[0]
        chunk_base_name = base_name + '_data_chunk'
        tree_file_name = 'tree.json'
        
        # file exists
        result_mat = load_mat(sys.argv[1])
        tree_structure = []
        
        # we need a map between the variable name and the generated files
        chunks = {}
        num_of_var_in_a_chunk = 20
        var_counter = 0
        num_of_chunks = 0
        

        # first one
        result_json = {}
        result_json["test_bench_name"] = "dummy data for now"
        result_json["test_bench_id"] = "dummy data for now 25"
        result_json["configuration"] = "dummy cfg for now 42"

        result_json["variables"] = []
        
        time_part = {}
        time_part["name"] = "time"
        time_part["unit"] = "s"
        time_part["desc"] = "Simulation time"
        time_part["data"] = []
        if 'time' in result_mat[0]:
            time_part["data"] = result_mat[0]["time"].tolist()
        elif 'Time' in result_mat[0]:
            time_part["data"] = result_mat[0]["time"].tolist()
        
        result_json["variables"].append(time_part)
        
        for item in result_mat[1]:
            var_counter = var_counter + 1

            chunk_name = chunk_base_name + str(num_of_chunks) + '.json'
            chunks[item] = chunk_name

            this_var = {}
            
            this_var["name"] = item
            this_var["unit"] = ""
            this_var["desc"] = ""
            this_var["data"] = result_mat[0][item].tolist()
            
            result_json["variables"].append(this_var)

            if (num_of_var_in_a_chunk == var_counter):
                with open(chunk_name,'wb') as file_out:
                    json.dump(result_json, file_out)
                num_of_chunks = num_of_chunks + 1
                var_counter = 0

                result_json = {}
                result_json["test_bench_name"] = "dummy data for now"
                result_json["test_bench_id"] = "dummy data for now 25"
                result_json["configuration"] = "dummy cfg for now 42"

                result_json["variables"] = []
                result_json["variables"].append(time_part)

        for name in result_mat[0]:
            # need to look up in the map and get the file name set to data_link
            data_link = chunks[name]
            getNode(name, tree_structure, data_link)
        
        #print tree_structure
        with open(tree_file_name,'wb') as file_out:
            json.dump(tree_structure, file_out)
        
    else:
        print 'First argument must be a .mat result file.'
    

