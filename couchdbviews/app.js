{
   "_id": "_design/app",
   "_rev": "2-d3402f8979558d54eb934ce680d87f4c",
   "filters": {
       "publications": "function(doc, req) { if (doc._deleted) { return true; } else { var types = ['article','book','chapter','generic']; if(types.indexOf(doc.type) != -1) { return true; } else { return false; } } }"
   }
}