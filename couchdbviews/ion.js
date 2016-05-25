{
   "_id": "_design/ion",
   "_rev": "4-00915c9ce39a7ee088b34f5904d66f95",
   "language": "javascript",
   "views": {
       "group": {
           "map": "function(doc) {\n  if (doc.type == 'nameCluster') { \n    // only do this for names that have publication details\n    if (doc.publication) { \t\n      if (doc.group) {\n        for(var i = 1; i <= doc.group.length; i++) {\n          path = doc.group.slice(0, i);\n\t  if (doc.year) {\n            emit([path,parseInt(doc.year[0])], 1);\n          }\n        }\n      }\n    }\n  }\n}",
           "reduce": "function (key, values, rereduce) {\n    return sum(values);\n}"
       }
   }
}