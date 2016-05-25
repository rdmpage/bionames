{
   "_id": "_design/identifier",
   "_rev": "4-07f15e7407af6a29f5ec53052605a47b",
   "language": "javascript",
   "views": {
       "doi": {
           "map": "function(doc) {\n       if (doc.identifier)\n       {\n          for (var i in doc.identifier)\n          {\n            if (doc.identifier[i].type == \"doi\")\n            {\n              emit(doc.identifier[i].id, doc._id);\n            }\n          }\n       }\n\n\n}"
       },
       "pmid": {
           "map": "function(doc) {\n       if (doc.identifier)\n       {\n          for (var i in doc.identifier)\n          {\n            if (doc.identifier[i].type == \"pmid\")\n            {\n              emit(parseInt(doc.identifier[i].id), doc._id);\n            }\n          }\n       }\n\n\n}"
       },
       "citation": {
           "map": "function(doc) {\n if (doc.title)\n{\n  var citation = '';\n\n  if (doc.author)\n  {\n    for (var i in doc.author)\n    {\n      var author = '';\n      if (doc.author[i].lastname)\n      {\n        author += doc.author[i].lastname;\n      }\n      if (doc.author[i].forename)\n      {\n        author += ', ' + doc.author[i].forename;\n      }\n      if (author != '')\n      {\n        citation += author + '; ';\n      }\n    }\n  }\n\n \n       \n\n\n  if (doc.year)\n  {\n    citation += '(' + doc.year + ') ';\n  }\n\n  citation += doc.title;\n  if (doc.journal)\n  {\n    citation += '. ' + doc.journal.name;\n    if (doc.journal.volume)\n    {\n      citation += ' ' + doc.journal.volume;\n    }\n   if (doc.journal.issue)\n   {\n      citation += '(' + doc.journal.issue + ')';\n    }\n   if (doc.journal.pages)\n   {\n      citation += ':' + doc.journal.pages.replace(/--/, '-');\n    }\n  }\n  emit(doc._id, citation);\n  \n}\n\n\n}"
       },
       "issn": {
           "map": "function(doc) {\n       if (doc.journal)\n       {\n         if (doc.journal.identifier)\n         {\n         for (var i in doc.journal.identifier)\n          {\n            if (doc.journal.identifier[i].type == \"issn\")\n            {\n              emit(doc.journal.identifier[i].id, doc._id);\n            }\n          }\n         }\n       }\n\n\n}"
       },
       "biostor": {
           "map": "function(doc) { if (doc.identifier) {    for (var i in doc.identifier)    {      if (doc.identifier[i].type == \"biostor\")      {        emit(doc.identifier[i].id, doc._id);      }    } }\n\n\n}"
       },
       "isbn": {
           "map": "function(doc)\n{ \n  if (doc.identifier) \n  {\n    for (var i in doc.identifier)\n    {\n      if (doc.identifier[i].type == \"isbn\")\n      {\n        emit(doc.identifier[i].id, doc._id);\n      }\n    }\n }\n}"
       },
       "googleBooks": {
           "map": "function(doc)\n{ \n  if (doc.identifier) \n  {\n    for (var i in doc.identifier)\n    {\n      if (doc.identifier[i].type == \"googleBooks\")\n      {\n        emit(doc.identifier[i].id, doc._id);\n      }\n    }\n }\n}"
       },
       "pmc": {
           "map": "function(doc) {\n       if (doc.identifier)\n       {\n          for (var i in doc.identifier)\n          {\n            if (doc.identifier[i].type == \"pmc\")\n            {\n              emit(parseInt(doc.identifier[i].id), doc._id);\n            }\n          }\n       }\n\n\n}"
       },
       "handle": {
           "map": "function(doc) {\n       if (doc.identifier)\n       {\n          for (var i in doc.identifier)\n          {\n            if (doc.identifier[i].type == \"handle\")\n            {\n              emit(doc.identifier[i].id, doc._id);\n            }\n          }\n       }\n\n\n}"
       },
       "url": {
           "map": "function(doc) {\n       if (doc.link)\n       {\n          for (var i in doc.link)\n          {\n            if (doc.link[i].anchor == \"LINK\")\n            {\n              emit(doc.link[i].url, doc._id);\n            }\n          }\n       }\n}"
       },
       "pdf": {
           "map": "function(doc) {\n       if (doc.link)\n       {\n          for (var i in doc.link)\n          {\n            if (doc.link[i].anchor == \"PDF\")\n            {\n              emit(doc.link[i].url, doc._id);\n            }\n          }\n       }\n}"
       },
       "cinii": {
           "map": "function(doc) { if (doc.identifier) {    for (var i in doc.identifier)    {      if (doc.identifier[i].type == \"cinii\")      {        emit(doc.identifier[i].id, doc._id);      }    } }\n\n\n}"
       },
       "jstor": {
           "map": "function(doc) { if (doc.identifier) {    for (var i in doc.identifier)    {      if (doc.identifier[i].type == \"jstor\")      {        emit(parseInt(doc.identifier[i].id), doc._id);      }    } }\n\n\n}"
       },
       "lsid": {
           "map": "/*\n  Output LSIDs\n*/\n\nfunction(doc) {\n  // ION LSIDs\n  if (doc.type == 'nameCluster') {\n    for (i in doc.names) {\n      emit(doc.names[i].id, doc._id);\n    }\n  }\n}\n"
       },
       "zoobank": {
           "map": "function(doc) {\n       if (doc.identifier)\n       {\n          for (var i in doc.identifier)\n          {\n            if (doc.identifier[i].type == \"zoobank\")\n            {\n              emit(doc.identifier[i].id, doc._id);\n            }\n          }\n       }\n\n\n}"
       }
   }
}