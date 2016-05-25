{
   "_id": "_design/references",
   "_rev": "10-6f0287c4ae5dd26ad0cb4811d7236b31",
   "language": "javascript",
   "views": {
       "cited_by": {
           "map": "function(doc) {\n  if (doc.references) {\n     for (var i in doc.references) {\n        var num_identifiers = 0;\n        if (doc.references[i].identifier) {\n           num_identifiers = doc.references[i].identifier.length;\n        }\n        if (num_identifiers != 0) {\n           for (var j in doc.references[i].identifier) {\n                emit(doc.references[i].identifier[j], i);\n            }\n         } else {\n            // If no external identifier then add key using BioNames\n            if (doc.references[i]._id) {\n              emit({\"type\": \"bionames\", \"id\" : doc.references[i]._id}, i);\n            }\n         }\n      }\n  }\n}"
       },
       "citation_versions": {
           "map": "/* For each cited document that we've identified in BioNames emit the citation so we can compare versions */\nfunction(doc) {\n  if (doc.references) {\n     for (var i in doc.references) {\n        /* Extract references that are mapped to a publication in database */\n        if (doc.references[i]._id) {\n\n          var citation = {};\n         if (doc.references[i].title) {\n            citation.title = doc.references[i].title;\n          }\n        if (doc.references[i].type) {\n            citation.type = doc.references[i].type;\n          }\n        if (doc.references[i].author) {\n            citation.author = doc.references[i].author;\n          }\n        if (doc.references[i].book) {\n            citation.book = doc.references[i].book;\n          }\n        if (doc.references[i].year) {\n            citation.year = doc.references[i].year;\n          }\n \n          if (doc.references[i].journal) {\n            citation.journal = doc.references[i].journal;\n          }\n           if (doc.references[i].identifier) {\n            citation.identifier = doc.references[i].identifier;\n          }          /* Emit the id of the publication and local bibliographical details */\n           emit(doc.references[i]._id, citation);\n          }\n      }\n  }\n}"
       },
       "citation_not_mapped": {
           "map": "function(doc) {\n  var types = ['article','book','chapter','generic'];\n  var type = types.indexOf(doc.type);\n  if (type != -1) {\n     if (doc.references) {\n        for (var i in doc.references) {\n            if (doc.references[i]._id) {\n                // we have mapped this reference\n            } else {\n                // haven't found this reference\n\n                var citation = {};\n                if (doc.references[i].title) {\n                    citation.title = doc.references[i].title;\n                }\n                if (doc.references[i].type) {\n                    citation.type = doc.references[i].type;\n                }\n                if (doc.references[i].author) {\n                    citation.author = doc.references[i].author;\n                }\n                if (doc.references[i].book) {\n                    citation.book = doc.references[i].book;\n                }\n                if (doc.references[i].year) {\n                    citation.year = doc.references[i].year;\n                }\n\n                if (doc.references[i].journal) {\n                    citation.journal = doc.references[i].journal;\n                }\n                if (doc.references[i].identifier) {\n                    citation.identifier = doc.references[i].identifier;\n                } /* Emit the id of the publication and local bibliographical details */\n                emit(doc.references[i]._id, citation);\n            }\n        }\n    }\n  }\n}\n"
       }
   }
}