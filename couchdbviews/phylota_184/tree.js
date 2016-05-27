{
   "_id": "_design/tree",
   "_rev": "15-abc77b3966c8ee017412168cc66085f2",
   "language": "javascript",
   "views": {
       "nexus": {
           "map": "// trees indexed by publication identifier\n\nfunction(doc) {\n  if (doc.tree) {\n    var nexus = \"#NEXUS\\n\";\n    nexus += \"BEGIN TREES;\\n\";\n\n    if (doc.translations.taxa) {\n      nexus += \"\\tTRANSLATE\\n\";\n      var first = true;\n      for (var i in doc.translations.taxa) {\n        if (first) {\n          first = false;\n        } else {\n          nexus += \",\\n\";\n        }\n        nexus += \"\\t\\t\" + i + \" '\" + doc.translations.taxa[i].replace(/'/g, \"''\") + \"'\";\n      }\n      nexus += \";\\n\";\n    }\n\n    nexus += \"\\tTREE \" + doc._id.replace(\"phylota/\", \"\") + ' = ' + doc.tree.newick + \"\\n\";\n    nexus += \"ENDBLOCK;\\n\";\n\n    emit(doc._id, nexus);\n  }\n}\n"
       },
       "tax_id": {
           "map": "// trees indexed by publication identifier\n\nfunction(doc) {\n  if (doc.phylota) {\n     emit(doc.phylota.ti, 1);\n  }\n}\n",
           "reduce": "function (key, values, rereduce) {\n    return sum(values);\n}"
       },
       "taxa": {
           "map": "// emit all taxa ids in each tree\n\nfunction(doc) {\n if (doc.tree) {\n     if (doc.translations.tax_id) {\n       for (var i in doc.translations.tax_id) {\n         emit([doc._id,doc.translations.tax_id[i]], 1);\n       }\n    }\n  }\n}",
           "reduce": "function (key, values, rereduce) {\n    return sum(values);\n}"
       },
       "newick": {
           "map": "// trees indexed by NCBI tax_id\n\nfunction(doc) {\n  if (doc.tree) {\n    // index by ti_root\n    emit(doc.phylota.ti, doc.tree.newick);\n\n    /*\n    // index by taxa in tree\n    if (doc.translations.tax_id) {\n       for (var i in doc.translations.tax_id) {\n         emit(doc.translations.tax_id[i], doc.tree.newick);\n       }\n    }\n    */\n\n  }\n}\n"
       },
       "tags": {
           "map": "// tags for trees \n\nfunction(doc) {\n  if (doc.tree) {\n    if (doc.tags) {\n      emit(doc.phylota.ti, doc.tags);\n    }\n  }\n}\n"
       }
   }
}