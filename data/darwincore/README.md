Darwin Core Archive
-------------------

## Build Darwin Core Archive dump for BioNames

See [Darwin Core Text Guide](http://rs.tdwg.org/dwc/terms/guides/text/index.htm) for background, and [EOL Content Partners: Contribute Using Archives](http://eol.org/info/329). Can validate using [EOL Archive and Spreadsheet Validator](http://services.eol.org/dwc_validator/)

As per [EOL Deliverable](https://trello.com/c/dwoZ193L) generate a Darwin Core archive file containing taxa, references, and link between taxa and reference.

## Generate Darwin Core Archive from CouchDB

Use list views.

	curl 'http://direct.bionames.org:5984/bionames/_design/darwincorearchive/_list/publication_tsv/publication' > references.tsv

	curl 'http://direct.bionames.org:5984/bionames/_design/darwincorearchive/_list/taxa_tsv/taxa' > taxa.tsv

Create meta.xml by hand, then create archive:

	zip bionames-dwca.zip meta.xml taxa.tsv references.tsv

