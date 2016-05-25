{
   "_id": "_design/count",
   "_rev": "2-d7cf203ac58659864c4c8a8815c099f2",
   "language": "javascript",
   "views": {
       "publications_by_year": {
           "map": "function(doc) \n{ \n  var types = ['article','book','chapter','generic','journal'];\n  var type = types.indexOf(doc.type);\n  if (type != -1) {\n    if (doc.year) {\n      if (isNaN(doc.year)) {\n      } else {\n        emit(parseInt(doc.year), 1);\n      }\n    }\n  }\n}",
           "reduce": "function (key, values, rereduce) {\n    return sum(values);\n}"
       },
       "names_by_year": {
           "map": "function(doc) \n{ \n  if (doc.type == 'nameCluster')\n  {\n    var weight = doc.year.length;\n    for (i in doc.year)\n    {\n       emit(doc.year[i], 1/weight);\n   }\n  }\n}",
           "reduce": "function (key, values, rereduce) {\n    return sum(values);\n}"
       },
       "identifier": {
           "map": "function(doc) { \n  if (doc.identifier) {    \n    for (var i in doc.identifier) {\n       if (doc.identifier[i].type) {\n        emit(doc.identifier[i].type, 1);\n      }     \n    }     \n  }\n}",
           "reduce": "function (key, values, rereduce) {\n    return sum(values);\n}"
       },
       "link": {
           "map": "function(doc) \n{ if (doc.link) \n {    \n  for (var i in doc.link)    \n  {      \n    emit(doc.link[i].anchor, 1);     \n   }     \n  }\n}",
           "reduce": "function (key, values, rereduce) {\n    return sum(values);\n}"
       },
       "document": {
           "map": "function(doc) \n{ \nif (doc.type) { \n     emit(doc.type, 1);     \n    \n  }\n}",
           "reduce": "function (key, values, rereduce) {\n    return sum(values);\n}"
       }
   }
}