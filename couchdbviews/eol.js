{
   "_id": "_design/eol",
   "_rev": "3-0cc810d220712afdbfd9aa4e6cb28c8f",
   "language": "javascript",
   "views": {
       "ncbi": {
           "map": "function(doc) {\n  if (doc.type == 'taxonConcept') {\n    if (doc.taxonConcepts) {\n      for (var i in doc.taxonConcepts) {\n        if (doc.taxonConcepts[i].nameAccordingTo == \"NCBI Taxonomy\") {\n          emit('ncbi/' + doc.taxonConcepts[i].sourceIdentfier, parseInt(doc.identifier));\n        }\n      }\n    }\n  } \n}"
       },
       "ncbi_thumbnail": {
           "map": "function(doc) {\n  if (doc.type == 'taxonConcept') {\n    if (doc.taxonConcepts) {\n      for (var i in doc.taxonConcepts) {\n        if (doc.taxonConcepts[i].nameAccordingTo == \"NCBI Taxonomy\") {\n          // do we have images?\n          if (doc.dataObjects) {\n            for (var j in doc.dataObjects) {\n              if (doc.dataObjects[j].dataType == 'http://purl.org/dc/dcmitype/StillImage') {\n                if (doc.dataObjects[j].eolThumbnailURL) {\n                  var imageUrl = doc.dataObjects[j].eolThumbnailURL;\n                  \n                  emit('ncbi/' + doc.taxonConcepts[i].sourceIdentfier, imageUrl);\n                }\n              }\n            }\n          }\n        }\n      }\n    }\n  } \n}"
       },
       "provider_id": {
           "map": "// Taxon concepts that correspond to an EOL taxon page\nfunction(doc) {\n  if (doc.type == 'taxonConcept') {\n    // Mappings that EOL knows about\n    if (doc.taxonConcepts) {\n      for (var i in doc.taxonConcepts) {\n        if (doc.taxonConcepts[i].nameAccordingTo == \"NCBI Taxonomy\") {\n          emit(parseInt(doc.identifier), 'ncbi/' + doc.taxonConcepts[i].sourceIdentfier);\n        }\n      }\n    }\n    // Mappings that EOL doesn't know about\n    if (doc.identifier) {\n    \tif (doc.identifier.eol) {\n           emit(parseInt(doc.identifier.eol[0]), doc._id);\n        }\n    }\n   } \n}"
       }
   }
}