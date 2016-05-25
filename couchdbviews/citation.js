{
   "_id": "_design/citation",
   "_rev": "2-ca1df1a2d19b76aec04e285e1825b12c",
   "language": "javascript",
   "indexes": {
       "all": {
           "index": "function(doc) { if (doc.citation_string) {  index(\"default\", doc.citation_string, {\"store\": \"yes\"}); } }"
       },
       "journal": {
           "index": "function(doc) { if (doc.journal) {  index(\"default\", doc.journal.name, {\"store\": \"yes\"}); } }"
       },
       "general": {
           "index": "function(doc) { if (doc.type == 'nameCluster') { index(\"default\", doc.nameComplete, { \"store\": \"yes\" }); if (doc.rankString) { index(\"rank\", doc.rankString, { \"facet\": true }); } if (doc.year) { index(\"year\", doc.year, { \"facet\": true }); } } }"
       }
   }
}