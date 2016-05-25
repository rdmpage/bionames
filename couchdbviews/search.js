{
   "_id": "_design/search",
   "_rev": "3-7663a4ab579aea12093085a8296dfceb",
   "language": "javascript",
   "indexes": {
       "all": {
           "index": "function(doc) { if (doc.nameComplete) {  index(\"name\", doc.nameComplete, {\"store\": \"yes\"}); }  if (doc.canonicalName) {  index(\"canonicalName\", doc.canonicalName, {\"store\": \"yes\"}); } if (doc.type) { index(\"type\", doc.type, {\"store\": \"yes\"}); } }"
       }
   },
   "views": {
       "short": {
           "map": "/*\nIndex the short strings we are likely to search on\n*/\n\nfunction(doc) {\n\n  //------------- Publications----------------\n  var types = ['article','book','chapter','generic','journal', 'thesis'];\n  var type = types.indexOf(doc.type);\n  if (type != -1) \n  {\n    // BHL names \n    if (doc.names)\n    {\n\tvar title = '[unknown]';\n        if (doc.title) {\n          title = doc.title;\n        }\n       for (j in doc.names)\n       {\n         emit(doc.names[j].namestring, { \"id\": doc._id, \"term\": title, \"type\": doc.type });\n         \n         var pattern = /(\\w+) (\\w+)/;\n         var match = pattern.exec(doc.names[j].namestring);\n         if (match) {\n           emit(match[1], {\"id\": doc._id, \"term\": title, \"type\": doc.type });\n        }\n       }\n    }\n    \n    // Authors?\n}\n\n  //--------------Authors--------------------\n\n\n  //--------------Concepts--------------------\n  if(doc.type == 'taxonConcept') {   \n       // name (remember, NCBI name might not be parseable)\n       var conceptName = '';\n       if (doc.canonicalName) {\n         conceptName = doc.canonicalName;\n       } else {\n         conceptName = doc.scientificName;\n       } \n       emit(conceptName, {\"id\": doc._id, \"term\": conceptName, \"type\": doc.type });\n \n       // taxon author\n       if (doc.author) {\n          var author =  doc.author;\n          author = author.replace(/\\(/, '');\n          author = author.replace(/\\)/, '');  \t\n    \t  emit(author, {\"id\": doc._id, \"term\": doc.scientificName, \"type\": doc.type });       \n       }\n       \n       // synonyms (remember, NCBI name might not be parseable)\n       if (doc.synonyms) {\n          for (var i in doc.synonyms) {\n            var synonymName = '';\n            if (doc.synonyms[i].canonicalName) {\n              synonymName = doc.synonyms[i].canonicalName;\n            } else {\n              synonymName = doc.synonyms[i].scientificName;\n            } \n\n            if (conceptName != synonymName) {\n              emit(conceptName, {\"id\": doc._id, \"term\": synonymName, \"type\": doc.type });\n              emit(synonymName, {\"id\": doc._id, \"term\": conceptName, \"type\": doc.type });\n            }\n \n          }\n       }\n   }\n\n\n  //------------- Name cluster----------------\n  if (doc.type == 'nameCluster')\n  {\n    // publication(s) linked to this name cluster\n    for (var i in doc.names) {\n      if (doc.names[i].publishedInCitation) {\n        emit(doc.nameComplete, { \"id\": doc.names[i].publishedInCitation, \"term\": doc.names[i].publication, \"type\": 'article' }); // hack\n        \n        var pattern = /(\\w+) (\\w+)/;\n        var match = pattern.exec(doc.nameComplete);\n        if (match) {\n          emit(match[1], {\"id\": doc.names[i].publishedInCitation, \"term\": doc.names[i].publication, \"type\": 'article' }); // hack\n        }\n      }\n    }\n  \n    // 0. authority + date\n    if (doc.taxonAuthor) {\n        var pattern = /(.*)\\s+([1|2][0-9]{3})$/;\n          var match = pattern.exec(doc.taxonAuthor);\t\t\t\t\t\t\t\t\t\n          if (match) {\n            emit(doc.taxonAuthor, {\"id\": doc._id, \"term\": doc.nameComplete, \"type\": doc.type });\n          }\n     }\n\n  \t// Taxon name string\n    // 1. emit name as is\n    emit(doc.nameComplete, {\"id\": doc._id, \"term\": doc.nameComplete, \"type\": doc.type });\n    \n    // 2. handle multiple ways we can write the \"same\" name\n    if (doc.uninomial) \n    {\n      // ignore \n    } \n    else \n    {\n      if (doc.rankString)\n      {\n        switch(doc.rankString)\n        {\n          // For subgenus we index the infragenericEpithet \n          // so we can find it if promoted to genus\n          case 'subgenus':\n            if (doc.infragenericEpithet)\n            {\n               emit(doc.infragenericEpithet, {\"id\": doc._id, \"term\": doc.nameComplete, \"type\": doc.type });\n            } \n            break;\n\n          // index species with and without subgenus,\n          // also output genus\n          case 'species':\n            // genus (test it exists as name parser may have broken, e.g. genus? names)\n\t    if (doc.genusPart) {\n              emit(doc.genusPart, {\"id\": doc._id, \"term\": doc.nameComplete, \"type\": doc.type });\n            }\n            if (doc.infragenericEpithet)\n            {  \n               // subgenus by itself \n              emit(doc.infragenericEpithet, {\"id\": doc._id, \"term\": doc.nameComplete, \"type\": doc.type });\n   \n\n             // genus + species\n               var str = doc.genusPart + ' ' + doc.specificEpithet;\n\n               if (str != doc.nameComplete)\n               {\n                 emit(str, {\"id\": doc._id, \"term\": doc.nameComplete, \"type\": doc.type });\n               }\n               // subgenus + species\n               str = doc.infragenericEpithet + ' ' + doc.specificEpithet;\n\n               if (str != doc.nameComplete)\n               {\n                 emit(str, {\"id\": doc._id, \"term\": doc.nameComplete, \"type\": doc.type });\n               }\n            }\n            break;\n\n          // index subspecies with and without species and without subgenus\n          case 'subspecies':\n            if (doc.infragenericEpithet)\n            {  \n               // genus + species + subspecies\n               emit(doc.genusPart + ' ' + doc.specificEpithet + ' ' + doc.infraspecificEpithet, {\"id\": doc._id, \"term\": doc.nameComplete, \"type\": doc.type });\n            }\n            // genus + subspecies\n            emit(doc.genusPart + ' ' + doc.infraspecificEpithet, {\"id\": doc._id, \"term\": doc.nameComplete, \"type\": doc.type });\n            break;\n \n          default:\n            break;\n         }\n      }\n        //------------------------------------------------------------------------------------------\n \t    // Epithet search\n \t    var epithet = '';\n    \t\n    \tif (doc.infraspecificEpithet) {\n    \t\tepithet = doc.infraspecificEpithet;\n    \t} else if (doc.specificEpithet) {\n    \t\tepithet = doc.specificEpithet;\n    \t}\n    \t if (epithet != '') {\n    \tif (doc.taxonAuthor) { \n          var author =  doc.taxonAuthor;\n          author = author.replace(/\\(/, '');\n          author = author.replace(/\\)/, '');  \t\n    \t  emit(epithet + ' ' + author, {\"id\": doc._id, \"term\": doc.nameComplete, \"type\": doc.type });\n\n          \n\n          // if author has date index authorshp without date\n          var pattern = /(.*)\\s+([1|2][0-9]{3})$/;\n          var match = pattern.exec(author);\t\t\t\t\t\t\t\t\t\n          if (match) {\n            emit(epithet + ' ' + match[1], {\"id\": doc._id, \"term\": doc.nameComplete, \"type\": doc.type });\n          }\n          \n    \t} else {\n          // epithet without taxon author\n    \t  emit(epithet, {\"id\": doc._id, \"term\": doc.nameComplete, \"type\": doc.type });\n      \n        }\n       }\n      \n      \n      \n    }\n \n}\n}"
       }
   }
}