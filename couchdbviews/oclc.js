{
   "_id": "_design/oclc",
   "_rev": "1-3630e044e2af7984fb2056d9211dd06a",
   "language": "javascript",
   "views": {
       "identifier": {
           "map": "function(doc) \n{\n  if (doc.journal) \n  {   \n    if (doc.journal.identifier)   \n    {   \n      for (var i in doc.journal.identifier)    \n      {      \n         if (doc.journal.identifier[i].type == \"oclc\")      \n         {        \n           var key = [];\n           key.push(parseInt(doc.journal.identifier[i].id));\n\n           if (doc.year)\n           {\n              var year = parseInt(doc.year);\n              if (isNaN(year))\n              {\n                key.push(\"unknown\");\n              }\n              else\n              {\n                key.push(year);\n              }\n           }\n           else\n           {\n             key.push(\"unknown\");\n           }\n           \n           var identifiers = [];\n           \n           // list all identifiers for this article\n\t\t\tif (doc.identifier) \n\t\t\t{    \n\t\t\t  for (var i in doc.identifier)    \n\t\t\t  {\n\t\t\t  \tidentifiers.push(doc.identifier[i].type);\n\t\t\t   }     \n\t\t\t}   \n\t\t\t\n\t\t\tif (doc.link) \n\t\t\t {    \n\t\t\t  for (var i in doc.link)    \n\t\t\t  {      \n\t\t\t\tidentifiers.push(doc.link[i].anchor);     \n\t\t\t   }     \n\t\t\t  }\t\t\t\n\t\t\t\t\t\t\n\t\t\t\n             emit(key, identifiers); \n         }    \n      }   \n    } \n  }\n}"
       },
       "year": {
           "map": "function(doc) \n{\n  if (doc.journal) \n  {   \n    if (doc.journal.identifier)   \n    {   \n      for (var i in doc.journal.identifier)    \n      {      \n         if (doc.journal.identifier[i].type == \"oclc\")      \n         {        \n           var key = [];\n           key.push(parseInt(doc.journal.identifier[i].id));\n\n           if (doc.year)\n           {\n              var year = parseInt(doc.year);\n              if (isNaN(year))\n              {\n              }\n              else\n              {\n                var decade = Math.floor(year/10) * 10;\n                key.push(decade);\n                key.push(year);\n\n                if (doc.journal.volume)\n                {\n                  key.push(doc.journal.volume);\n                }\n                else\n                {\n                  key.push(null);\n                }\n\n                var startingPage = null;\n                if (doc.journal.pages)\n                {\n                   var delimiter = doc.journal.pages.indexOf('-');\n                   if (delimiter != -1)\n                   {\n              \t      startingPage = doc.journal.pages.substring(0, delimiter);\n                      if (isNaN(startingPage))\n                      {\n                      }\n                      else \n                      {\n                         startingPage = parseInt(startingPage);\n                      }\n                   }\n                   else\n                   {\n                      startingPage = doc.journal.pages;\n                   }\t\t\n                }\n                key.push(startingPage);\n \n                emit(key, 1); \n             }\n           }     \n         }    \n      }   \n    } \n  }\n}",
           "reduce": "function (key, values, rereduce) {\n    return sum(values);\n}"
       },
       "points": {
           "map": "function(doc) \n{\n  if (doc.geometry)\n  {\n    if (doc.journal) \n    {   \n      if (doc.journal.identifier)   \n      {   \n        for (var i in doc.journal.identifier)    \n        {      \n           if (doc.journal.identifier[i].type == \"oclc\")      \n           {        \n             for (var j in doc.geometry.coordinates)\n  \t\t\t{\n   \t\t\t\temit(parseInt(doc.journal.identifier[i].id), doc.geometry.coordinates[j]); \n            }\n          }\n        }\n      }\n    } \n  }\n}"
       },
       "count": {
           "map": "function(doc) { \n  if (doc.journal) \n  {  \n     if (doc.journal.identifier)   \n     {   \n        for (var i in doc.journal.identifier)    \n        {      \n           if (doc.journal.identifier[i].type == \"oclc\")      \n           {\n             emit([parseInt(doc.journal.identifier[i].id),doc.journal.name] , 1);\n           }    \n       }   \n     } \n  }\n}",
           "reduce": "function (key, values, rereduce) {\n    return sum(values);\n}"
       },
       "articles": {
           "map": "function(doc) \n{\n  if (doc.journal) \n  {   \n    if (doc.journal.identifier)   \n    {   \n      for (var i in doc.journal.identifier)    \n      {      \n         if (doc.journal.identifier[i].type == \"oclc\")      \n         {     \n            /*\n            var article = {};\n            if (doc.title) { article.title = doc.title; }\n            if (doc.year) { article.year = doc.year; }\n            if (doc.journal.volume) { article.volume = doc.journal.volume; }\n\n            if (doc.author) {\n              article.author = [];\n              for (var j in doc.author) {\n                if (doc.author[j].name) {\n                  article.author.push(doc.author[j].name);\n                }\n              }\n            }\n\n\t    if (doc.thumbnail) {\n              article.thumbnail_url = 'id/' + doc._id + '/thumbnail/image';\n            }\n            emit(doc.journal.identifier[i].id, article);  \n */\n            emit(parseInt(doc.journal.identifier[i].id), null);  \n         }    \n      }   \n    } \n  }\n}"
       }
   }
}