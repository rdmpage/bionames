{
   "_id": "_design/datamining",
   "_rev": "9-fa30450a92fe8fa81f3a75f2baeaeca7",
   "language": "javascript",
   "views": {
       "names_pages": {
           "map": "function(doc) {\n  if (doc.names) {\n    for (var i in doc.names) {\n       for (var j in doc.names[i].pages) {\n          emit(doc.names[i].namestring, doc.names[i].pages[j]);\n        }\n    }\n  } \n}\n"
       },
       "pages_names": {
           "map": "function(doc) {\n  if (doc.names) {\n    for (var i in doc.names) {\n       for (var j in doc.names[i].pages) {\n          emit(doc.names[i].pages[j], doc.names[i].namestring);\n        }\n    }\n  } \n}\n"
       },
       "names_pages_count": {
           "map": "function(doc) {\n  if (doc.names) {\n    for (var i in doc.names) {\n       for (var j in doc.names[i].pages) {\n          emit(doc.names[i].namestring, 1);\n        }\n    }\n  } \n}\n",
           "reduce": "function (key, values, rereduce) {\n    return sum(values);\n}"
       },
       "bhl_pages_count": {
           "map": "function(doc) {\n  if (doc.bhl_pages) {\n     emit(null, doc.bhl_pages.length);\n  } \n}\n",
           "reduce": "function (key, values, rereduce) {\n    return sum(values);\n}"
       },
       "pages_epithet": {
           "map": "function(doc) {\n  if (doc.names) {\n    for (var i in doc.names) {\n      var s = doc.names[i].namestring;\n      var pos = s.lastIndexOf(' ');\n      if (pos != -1) {\n        var epithet = s.substring(pos+1);\n        for (var j in doc.names[i].pages) {\n          emit(doc.names[i].pages[j], epithet);\n        }\n      }\n    }\n  } \n}\n"
       },
       "names_epithet": {
           "map": "function(doc) {\n  if (doc.names) {\n    for (var i in doc.names) {\n      var s = doc.names[i].namestring;\n      var pos = s.lastIndexOf(' ');\n      if (pos != -1) {\n        var epithet = s.substring(pos);\n        for (var j in doc.names[i].pages) {\n          emit(epithet, doc.names[i].pages[j]);\n        }\n      }\n    }\n  } \n}\n"
       }
   }
}