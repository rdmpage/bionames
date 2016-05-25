{
   "_id": "_design/classification",
   "_rev": "1-dcdf681e65608511e35d4f5e5ded07fa",
   "language": "javascript",
   "views": {
       "gbif_num_leaves": {
           "map": "function(doc) {\n\n  if(doc.type == 'taxonConcept')\n  {\n    // GBIF\n    if (doc.source == \"http://ecat-dev.gbif.org/checklist/1\")\n    {\n\n    if (doc.children)\n    {\n      // we only want count of total number of leaves that\n      // descend from this node\n    }\n    else\n    { \n      for (var i in doc.ancestors)\n      {\n         emit(doc.ancestors[i].sourceIdentifier, 1);\n      }\n    }\n  }\n  }\n\n}\n",
           "reduce": "function (key, values, rereduce) {\n    return sum(values);\n}"
       },
       "gbif_path": {
           "map": "/* Create view that indexes year counts by classification path */\nfunction(doc) {\n  if(doc.type == 'taxonConcept') {\n\t  if (doc.source == \"http://ecat-dev.gbif.org/checklist/1\") {\n\t   if (doc.identifier) {\n\t\t  if (doc.identifier.ion) { \t\t\t\t\t\n\t\t    for (var j in doc.identifier.ion) {\n\t\t\t  if (doc.identifier.ion[j].year) {\n\t\t\t    for (var i in doc.ancestors) {\n\t\t\t\t  emit([doc.ancestors[i].sourceIdentifier,parseInt(doc.identifier.ion[j].year[0])], 1);\n\t\t\t   }\n\t\t    }\n\t\t  }              \n\t\t}\n\t  }\n\t}   \n  }\n}"
       },
       "gbif_synonyms": {
           "map": "function(doc) {\n  if(doc.type == 'taxonConcept') {\n    if (doc.source == \"http://ecat-dev.gbif.org/checklist/1\") {\n       if (doc.synonyms) {\n          for (var i in doc.synonyms) {\n            emit(doc.canonicalName, doc.synonyms[i].canonicalName);\n            emit(doc.synonyms[i].canonicalName, doc.canonicalName);\n          }\n       }\n    }\n  }\n}"
       },
       "gbif_year": {
           "map": "/* Create view that indexes year counts for names by classification path */\nfunction(doc) {\n  var pattern = /\\s+([1|2][0-9]{3})$/;\n  if(doc.type == 'taxonConcept') {\n    if (doc.source == \"http://ecat-dev.gbif.org/checklist/1\") {\n     var years = new Array();\n     if (doc.identifier) {\n      if (doc.identifier.ion) {           \n        for (var j in doc.identifier.ion) {\n          if (doc.identifier.ion[j].year) {\n            years.push(doc.identifier.ion[j].year[0]);\n          }\n          else {\n            if (doc.identifier.ion[j].taxonAuthor) {\n              var match = pattern.exec(doc.identifier.ion[j].taxonAuthor);\t\t\t\t\t\t\t\t\t\n              if (match) {\n                years.push(match[1]);\n              }\n            }\n          }\n        }              \n      }\n    }\n    \n    // If no years from names try from taxon concept\n    if (years.length == 0) {\n      if (doc.author) {\n\t    var match = pattern.exec(doc.author);\t\t\t\t\t\t\t\t\t\n        if (match) {\n          years.push(match[1]);\n        } \n      }\n    } \n    \n    // emit years\n    for (var j in years) {\n\t\tfor (var i in doc.ancestors) {\n\t\t  emit([parseInt(doc.ancestors[i].sourceIdentifier),parseInt(years[j])], 1);\n\t\t}\n    }          \n    \n    \n  }   \n}\n}",
           "reduce": "function (key, values, rereduce) {\n    return sum(values);\n}"
       },
       "gbif_no_mapping": {
           "map": "/* Flag taxa with no taxon name mapping */\nfunction(doc) {\n   if(doc.type == 'taxonConcept') {\n    if (doc.source == \"http://ecat-dev.gbif.org/checklist/1\") {\n     var has_ion = false;\n     if (doc.identifier) {\n      if (doc.identifier.ion) {\n        has_ion = true;           \n       }\n     }\n     if (!has_ion) {\n     \temit(parseInt(doc.sourceIdentifier), 1);\n     }     \t \n   }   \n }\n}",
           "reduce": "function (key, values, rereduce) {\n    return sum(values);\n}"
       },
       "name_to_concept": {
           "map": "/* Map between taxon names and GBIF */\nfunction(doc) {\n   if(doc.type == 'taxonConcept') {\n //   if (doc.source == \"http://ecat-dev.gbif.org/checklist/1\") {\n //   if (doc.source == \"http://www.ncbi.nlm.nih.gov/taxonomy\") {\n     if (doc.identifier) {\n       if (doc.identifier.ion) {\n          for(var i in doc.identifier.ion) {\n             var mapping = {};\n             mapping.concept = doc._id;\n             mapping.nameComplete = doc.identifier.ion[i].nameComplete;\n             mapping.canonicalName = doc.canonicalName;\n             mapping.scientificName = doc.scientificName;\n             if (doc.identifier.eol) {\n               mapping.eol = doc.identifier.eol[0];\n             }\n             emit('cluster/' + i, mapping);\n          }\n       }\n     }\n   //}   \n }\n}"
       },
       "eol_to_ion": {
           "map": "/* Map EOL taxon id to name */\nfunction(doc) {\n  if(doc.type == 'taxonConcept') {\n    if (doc.identifier) {\n    \tif (doc.identifier.eol && doc.identifier.ion) {\n    \t  for (var i in doc.identifier.ion) {\n           emit(parseInt(doc.identifier.eol[0]), 'cluster/' + i);\n          }\n       }\n    }\n  }\n}"
       },
       "synonyms": {
           "map": "function(doc) {\n  if(doc.type == 'taxonConcept') {\n        if (doc.synonyms) {\n         var conceptName = '';\n         if (doc.canonicalName) {\n           conceptName = doc.canonicalName;\n         } else {\n           conceptName = doc.scientificName;\n         } \n          for (var i in doc.synonyms) {\n\n            var synonymName = '';\n            if (doc.synonyms[i].canonicalName) {\n              synonymName = doc.synonyms[i].canonicalName;\n            } else {\n              synonymName = doc.synonyms[i].scientificName;\n            } \n\n            emit(conceptName, synonymName);\n            emit(synonymName, conceptName);\n          }\n       }\n  }\n}"
       },
       "publishedInCitation": {
           "map": "/* Output publication ids for name(s) for concept */\nfunction(doc) {\n   if(doc.type == 'taxonConcept') {\n     if (doc.identifier) {\n       if (doc.identifier.ion) {\n          for(var i in doc.identifier.ion) {\n             if (doc.identifier.ion[i].publishedInCitation) {\n             \temit(doc._id, { \"nameComplete\" : doc.identifier.ion[i].nameComplete, \"publishedInCitation\" : doc.identifier.ion[i].publishedInCitation} );\n             }\n          }\n       }\n     }\n   }\n}"
       },
       "gbif_path_key": {
           "map": "/* Paths */\nfunction(doc) {\n  if(doc.type == 'taxonConcept') {\n    if (doc.source == \"http://ecat-dev.gbif.org/checklist/1\") {\n      if (doc.ancestors && doc.ancestors.length > 0) {\n        var key = [];\n\t  \n        for (var i in doc.ancestors) {\n          key.push(parseInt(doc.ancestors[i].sourceIdentifier));\n        }\n        key.push(parseInt(doc.sourceIdentifier));\n \n        var name = '';\n        if (doc.canonicalName) {\n          name = doc.canonicalName;\n        } else {\n          name = doc.scientificName;\n        }\n        emit(key, name);\n      }\n    }\n  }\n}   "
       },
       "namestring_to_concept": {
           "map": "/* Lookup table to find concept(s) for a name string */\nfunction(doc) {\n   if(doc.type == 'taxonConcept') {\n     if (doc.source == \"http://ecat-dev.gbif.org/checklist/1\") {\n       var name = '';\n       if (doc.canonicalName) {\n          name = doc.canonicalName;\n       } else {\n          name = doc.scientificName;\n       }\n       /*\n       if (doc.ancestors && doc.ancestors.length > 0) {\n        var path = [];\n\n        for (var i in doc.ancestors) {\n          path.push(parseInt(doc.ancestors[i].sourceIdentifier));\n        }\n        path.push(parseInt(doc.sourceIdentifier));\n        emit(name, path);\n        \n     }\n    */\n     emit(name, doc._id);\n       \n\n     }\n   }\n}"
       }
   }
}