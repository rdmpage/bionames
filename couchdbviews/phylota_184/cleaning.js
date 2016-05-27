{
   "_id": "_design/cleaning",
   "_rev": "1-619e8aa8adfeaa7a03ca5b418ab89202",
   "language": "javascript",
   "views": {
       "missing_gi": {
           "map": "// index trees by taxon id \n\nfunction(doc) {\n if (doc.tree) {\n     if (doc.missing_gi) {\n       for (var i in doc.missing_gi) {\n         emit(doc.missing_gi[i], 1);\n       }\n    }\n  }\n}",
           "reduce": "function (key, values, rereduce) {\n    return sum(values);\n}"
       }
   }
}