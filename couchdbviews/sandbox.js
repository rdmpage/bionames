{
   "_id": "_design/sandbox",
   "_rev": "3-980b60a9e67cad84913090e74de0cafc",
   "language": "javascript",
   "views": {
       "epithet": {
           "map": "function get_default_identifier(doc) {\n  var id = null;\n  if (doc.identifier) {\n    for(var i in doc.identifier) {\n       switch (doc.identifier[i].type) {\n         case 'doi':\n           if (!id) {\n             id = 'doi:' + doc.identifier[i].id;\n           }\n           break;\n         case 'biostor':\n           if (!id) {\n             id = 'biostor:' + doc.identifier[i].id;\n           }\n           break;\n         case 'handle':\n           if (!id) {\n             id = 'hdl:' + doc.identifier[i].id;\n           }\n           break;\n         case 'pmid':\n           if (!id) {\n             id = 'pmid:' + doc.identifier[i].id;\n           }\n         case 'cinii':\n           if (!id) {\n             id = 'cinii:' + doc.identifier[i].id;\n           }\n           break;\n          case 'jstor':\n           if (!id) {\n             id = 'jstor:' + doc.identifier[i].id;\n           }\n           break;\n        default:\n           break;\n       }\n    }\n  }\n  return id;\n\n}\n\nfunction stem_species_name(species) {\n    var stemmed_species = species;\n\n    /* \n    doi:10.1186/1471-2105-14-16\n\n    The stemming (equivalent) in Taxamatch equates \n    -a, -is -us, -ys, -es, -um, -as and -os when \n    they occur at the end of a species epithet \n    (or infraspecies) by changing them all to -a. \n    Thus (for example) the epithets “nitidus”, “nitidum”, \n    “nitidus” and “nitida” will all be considered \n    equivalent following this process.  \n\n    To this I've added -se and -sis, -ue and -uis\n    */\n    var matched = '';\n\n    // -se\n    if (matched == '') {\n        if (species.match(/se$/)) {\n            matched = 'se';\n        }\n    }\n    // -sis\n    if (matched == '') {\n        if (species.match(/sis$/)) {\n            matched = 'sis';\n        }\n    }\n\n    // -ue\n    if (matched == '') {\n        if (species.match(/ue$/)) {\n            matched = 'ue';\n        }\n    }\n    // -uis\n    if (matched == '') {\n        if (species.match(/uis$/)) {\n            matched = 'uis';\n        }\n    }\n\n\n    // -is\n    if (matched == '') {\n        if (species.match(/is$/)) {\n            matched = 'is';\n        }\n    }\n    // -us\n    if (matched == '') {\n        if (species.match(/us$/)) {\n            matched = 'us';\n        }\n    }\n    // -ys\n    if (matched == '') {\n        if (species.match(/ys$/)) {\n            matched = 'ys';\n        }\n    }\n    // -es\n    if (matched == '') {\n        if (species.match(/es$/)) {\n            matched = 'es';\n        }\n    }\n    // -um\n    if (matched == '') {\n        if (species.match(/um$/)) {\n            matched = 'um';\n        }\n    }\n    // -as\n    if (matched == '') {\n        if (species.match(/as$/)) {\n            matched = 'as';\n        }\n    }\n    // -os\n    if (matched == '') {\n        if (species.match(/os$/)) {\n            matched = 'os';\n        }\n    }\n\n\n\n    // stem\n    if (matched != '') {\n        var pattern = RegExp(matched + '$');\n        stemmed_species = stemmed_species.replace(pattern, 'a');\n    } else {\n        /* Tony's algorithm doesn't handle ii and i */\n        // -ii -i \n        if (species.match(/ii$/)) {\n            stemmed_species = stemmed_species.replace(/ii$/, 'i');\n        }\n    }\n\n    return stemmed_species;\n}\n\n\n\n// Output pairs of names with same epithet on same page\nfunction(doc) {\n // if (doc.names) {\n  if (doc.names) {\n\n    // Build list of species names for each page\n    var pages = {};\n    for (var i in doc.names) {   \n      \n      for (var j in doc.names[i].pages) {\n         var page_number = doc.names[i].pages[j];\n \n         if (!pages[page_number]) {\n            pages[page_number] = {};\n         }\n         \n         var handle = true;\n         \n         // Ignore names that start with abbreviated genus\n         if (doc.names[i].namestring.match(/^[A-Z]\\./)) {\n           handle = false;\n         }\n\n         // Ignore names that start with parentheses\n         if (doc.names[i].namestring.match(/^\\(/)) {\n           handle = false;\n         }\n         \n         // Ignore bogus names from GNDR\n         if (doc.names[i].namestring.match(/^Arten/)) {\n           handle = false;\n         } \n         \n         if (handle) {  \n\n           // clean \n           var namestring = doc.names[i].namestring;\n           namestring = namestring.replace(/var\\.\\s*/, '');\n           namestring = namestring.replace(/subsp\\.?\\s*/, ''); \n           // OCR\n           namestring = namestring.replace(/suhsp\\.?\\s+/, '');\n\n       \n           var delimiter = namestring.lastIndexOf(' ');\n           if (delimiter != -1) {\n             genus = namestring.substring(0, delimiter);\n             species = namestring.substring(delimiter+1);\n  \n             // handle epithets in names that have genus abbreviated as one letter\n             species = species.replace(/\\. /, '');\n             \n             stemmed_species = stem_species_name(species);\n\n//emit(stemmed_species, species);\n  \n             if (!pages[page_number][stemmed_species]) {\n               pages[page_number][stemmed_species] = [];\n             }\n             // ignore abbreviated generic names\n             if (genus.length > 1) {\n               pages[page_number][stemmed_species].push(namestring);\n            }\n          }\n        }\n        \n      } \n    }\n\n    // Do pairwise comparison within page\n    for (page_number in pages) {\n       for (species in pages[page_number]) {\n       \n          if (pages[page_number][species].length > 1) {\n            for (j in pages[page_number][species]) {\n              for (k in pages[page_number][species]) {\n                 if (j != k) {\n                  var s1 = pages[page_number][species][j];\n                  var s2 = pages[page_number][species][k];\n                  var id = get_default_identifier(doc);\n                  var year = doc.year;\n                  emit (s1,[s2, id, doc._id, page_number, species, year]);\n                  }\n                }\n              }\n            }\n          }\n        }\n    \n  }      \n}  "
       }
   }
}