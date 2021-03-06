bionames
========

BioNames project



#### ElasticSearch

If running CouchDB locally we need a fulltext search engine, such as Elastic Search.

Get ElasticSearch from [http://www.elasticsearch.org/download](http://www.elasticsearch.org/download)

Get CouchDB River plugin. Go to [https://github.com/elasticsearch/elasticsearch-river-couchdb/blob/master/README.md](https://github.com/elasticsearch/elasticsearch-river-couchdb/blob/master/README.md)
and follow instructions, i.e. in root of ElasticSearch folder run

    bin/plugin -install elasticsearch/elasticsearch-river-couchdb/1.2.0.
    
You should see something like this:

	> Installing elasticsearch/elasticsearch-river-couchdb/1.2.0...
	Trying http://download.elasticsearch.org/elasticsearch/elasticsearch-river-couchdb/elasticsearch-river-couchdb-1.2.0.zip...
	Downloading ...DONE
	Installed river-couchdb

Now, run ElasticSearch

	bin/elasticsearch -f
	
Then we need to filter the documents we want from CouchDB, in this case I want publications, so I create this view in CouchDB:

{
   "_id": "_design/app",
   "filters": {
       "publications": "function(doc, req) { var types = ['article','book','chapter','generic']; if(types.indexOf(doc.type) != -1) { return true; } else { return false; }}"
   }
}

This means we can get a list of publications that have changed:

http://localhost:5984/bionames/_changes?filter=app/publications

Now, we tell ElasticSearch to consume this

	curl -XPUT 'localhost:9200/_river/bionames/_meta' -d '{
		"type" : "couchdb",
		"couchdb" : {
			"host" : "localhost",
			"port" : 5984,
			"db" : "bionames",
			"filter" : "app/publications"
		},
		"index" : {
			"index" : "bionames",
			"type" : "bionames",
			"bulk_size" : "100",
			"bulk_timeout" : "10ms"
		}
	}'

Note, remove all line breaks before you use the above command.

### Querying

Now we are indexing the publications.

	http://localhost:9200/bionames/_search?q=replacement

	curl -XPOST 'localhost:9200/bionames/_search?pretty=true' -d ' { "query" : { "match" : { "title" : "New Zealand Bopyridae" } } }'
	
### Replication
	
Check that CouchDB can be accessed externally.


	netstat -an | grep 5984


You are looking for a line like this - see http://serverfault.com/questions/79453/why-cant-i-access-my-couchdb-instance-externally-on-ubuntu-9-04-server


	tcp4       0      0  *.5984                 *.*                    LISTEN     


#### Run replication

Local bionames database is source, remote instance (in this case cloudant) is target.


	curl http://localhost:5984/_replicate -H 'Content-Type: application/json' -d '{ "source": "bionames", "target": "https://<username>:<password>@rdmpage.cloudant.com/bionames", "continuous":true }'


You should get a response like this:


	{"ok":true,"_local_id":"7bf516ee63001a188a8186d7cf718194+continuous"}
	
Continuous replication can be expensive on Cloudant, so better to replicate manually:

		curl http://localhost:5984/_replicate -H 'Content-Type: application/json' -d '{ "source": "bionames", "target": "https://<username>:<password>@bionames.cloudant.com/bionames"}'

		curl http://localhost:5984/_replicate -H 'Content-Type: application/json' -d '{ "source": "archive", "target": "https://<username>:<password>@bionames.cloudant.com/archive"}'

