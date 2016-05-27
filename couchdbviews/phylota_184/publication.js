{
   "_id": "_design/publication",
   "_rev": "3-77afd67d661ce2320ec58c8bfad85b8c",
   "language": "javascript",
   "views": {
       "tree": {
           "map": "// trees indexed by publication identifier\n\nfunction(doc) {\n  if (doc.data_sources) {\n     for (var i in doc.data_sources) {\n       if (doc.data_sources[i].identifier) {\n          for (var j in doc.data_sources[i].identifier) {\n            switch (doc.data_sources[i].identifier[j].type) {\n              case 'doi':\n              case 'pmid':\n                emit(doc.data_sources[i].identifier[j].id, 1);\n                break;\n            }\n          }\n       }\n     }\n  }\n}",
           "reduce": "function (key, values, rereduce) {\n    return sum(values);\n}"
       },
       "citation_string": {
           "map": "// trees indexed by citation string\n\nfunction(doc) {\n  if (doc.data_sources) {\n     for (var i in doc.data_sources) {\n       if (doc.data_sources[i].citation_string) {\n         emit(doc.data_sources[i].citation_string, 1);\n        }\n     }\n  }\n}",
           "reduce": "function (key, values, rereduce) {\n    return sum(values);\n}"
       }
   }
}