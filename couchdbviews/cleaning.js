{
   "_id": "_design/cleaning",
   "_rev": "4-1e13c379fe4b33c71d7c8018c3e88205",
   "language": "javascript",
   "views": {
       "publications_without_author": {
           "map": "function(doc) \n{ \n  var types = ['article','book','chapter','generic','journal'];\n  var type = types.indexOf(doc.type);\n  if (type != -1) \n  {\n     if (!doc.author)\n     {\n        emit(doc.year, 1);\n     }\n  }\n}",
           "reduce": "function (key, values, rereduce) {\n    return sum(values);\n}"
       },
       "alldocs": {
           "map": "function(doc) { \n   emit(doc._id, 1);\n\n}",
           "reduce": "function (key, values, rereduce) {\n    return sum(values);\n}"
       },
       "concept_no_map": {
           "map": "/* Concepts not mapped */\nfunction(doc) {\n   if(doc.type == 'taxonConcept') {\n    if (doc.source == \"http://www.ncbi.nlm.nih.gov/taxonomy\") {\n\n     if (doc.identifier) {\n     }\n     else {\n       // only show proper names\n       if (doc.canonicalName) {\n         emit(doc._id, doc.canonicalName);\n       }\n    }\n    }\n  }\n}"
       },
       "doi_without_author": {
           "map": "function(doc) { \n  var types = ['article','book','chapter','generic','journal'];\n  var type = types.indexOf(doc.type);\n  if (type != -1) {\n    if (doc.identifier) {\n       for (var i in doc.identifier) {\n          if (doc.identifier[i].type == 'doi') {   \n            if (!doc.author) {\n               emit(doc._id, 1);\n            }\n          }\n        }\n     }\n   }\n}",
           "reduce": "function (key, values, rereduce) {\n    return sum(values);\n}"
       },
       "jstor_without_thumbnail": {
           "map": "/* Articles in JSTOR that don't have thumbnails */\nfunction(doc) { \n  var types = ['article','book','chapter','generic','journal'];\n  var type = types.indexOf(doc.type);\n  if (type != -1) {\n    if (doc.identifier) {\n       var jstor = false;\n       for (var i in doc.identifier) {\n          if (doc.identifier[i].type == 'jstor') {\n             jstor = true;\n          }\n          if (doc.identifier[i].type == 'doi') {   \n            if (doc.identifier[i].id.match(/10.2307/)) {\n              jstor = true;\n            }\n          }\n        }\n        \n        if (jstor && !doc.thumbnail) {\n        \temit(doc._id, 1);\n        }\n     }\n   }\n}",
           "reduce": "function (key, values, rereduce) {\n    return sum(values);\n}"
       },
       "author_character_encoding": {
           "map": "function(doc) {\n  if (doc.author) {\n    for (var i in doc.author) {\n      if (doc.author[i].name) {\n        if (doc.author[i].name.indexOf('�') != -1) {\n           emit(doc.author[i].name, 1);\n        }\n      }\n    }\n  }\n}",
           "reduce": "function (key, values, rereduce) {\n    return sum(values);\n}"
       }
   }
}