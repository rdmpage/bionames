{
   "_id": "_design/genus",
   "_rev": "2-b866255227c6ab04afaa956d59978e24",
   "language": "javascript",
   "views": {
       "species_year": {
           "map": "/*\nIndex species and subspecies by genus name and year so we can answer questions about recently described species,\ne.g. \"were there any new species of Rattus described since 2000?\"\n*/\n\nfunction(doc) {\n\n  //------------- Name cluster----------------\n  if (doc.type == 'nameCluster') {\n      if (doc.rankString && doc.year) {\n        switch(doc.rankString)\n        {\n          // index species/subspecies with and without subgenus,\n          case 'species':\n          case 'subspecies':\n            // genus (test it exists as name parser may have broken, e.g. genus? names)\n\t        if (doc.genusPart) {\n              emit([doc.genusPart, parseInt(doc.year[0])], doc.nameComplete);\n            }\n            // if we have subgenus then create a key for that as well\n            if (doc.infragenericEpithet) {\n              emit([doc.infragenericEpithet, parseInt(doc.year[0])], doc.nameComplete);\n            }\n            break;\n\n          default:\n            break;\n         }\n      }\n  }\n}\n"
       }
   }
}