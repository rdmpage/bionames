{
   "_id": "_design/taxa",
   "_rev": "7-ebfef822bbbd20e50aed87aeafb5a5b8",
   "language": "javascript",
   "views": {
       "tree": {
           "map": "// index trees by taxon name \n\nfunction(doc) {\n if (doc.tree) {\n     if (doc.translations.taxa) {\n       for (var i in doc.translations.taxa) {\n         var nameString = doc.translations.taxa[i];\n         emit([nameString, doc._id], 1);\n\n         // emit genus part of multinomial\n\t var matches = nameString.match(/^[\"]?([A-Z]\\w+)\\s/);\n         if (matches) {\n           emit([matches[1], doc._id], 1);\n         }\n       }\n    }\n  }\n}",
           "reduce": "function (key, values, rereduce) {\n    return sum(values);\n}"
       },
       "tax_id_tree": {
           "map": "// index trees by taxon id \n\nfunction(doc) {\n if (doc.tree) {\n     if (doc.translations.tax_id) {\n       for (var i in doc.translations.tax_id) {\n         emit([doc.translations.tax_id[i], doc._id], 1);\n       }\n    }\n  }\n}",
           "reduce": "function (key, values, rereduce) {\n    return sum(values);\n}"
       }
   }
}