{
   "_id": "_design/geojson",
   "_rev": "1-298db3821239b4d4f305a9f2ee6d6bce",
   "spatial": {
       "points": "function(doc) { if (doc.geometry) { emit(doc.geometry, doc._id);} }",
       "points2": "function(doc) { if (doc.geometry) { for (var i in doc.geometry.coordinates)  { emit({ type: 'Point', coordinates: [doc.geometry.coordinates[i][0], doc.geometry.coordinates[i][1]]}, doc._id); } } }"
   }
}