{
   "_id": "_design/geometry",
   "_rev": "1-da1cf49a224679fe64f79413adea9faf",
   "language": "javascript",
   "views": {
       "points": {
           "map": "function(doc) \n{\n if (doc.geometry) \n { \n  for (var i in doc.geometry.coordinates)\n  {\n   emit(doc.geometry.coordinates[i], null); \n  }\n }\n}",
           "reduce": "function(keys, values) {\n  return true;\n}"
       },
       "coordinates": {
           "map": "function(doc) {\n  if (doc.geometry) \n  { \n    for (var i in doc.geometry.coordinates) \n    { \n       emit({ type: 'Point', coordinates: [doc.geometry.coordinates[i][0], doc.geometry.coordinates[i][1]]}, doc._id); \n    } \n  }\n}"
       }
   }
}