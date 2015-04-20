Darwin Core Archive
-------------------

## Build Darwin Core Archive dump for BioNames

See [Darwin Core Text Guide](http://rs.tdwg.org/dwc/terms/guides/text/index.htm) for background, and [EOL Content Partners: Contribute Using Archives](http://eol.org/info/329). Can validate using [EOL Archive and Spreadsheet Validator](http://services.eol.org/dwc_validator/)

As per [EOL Deliverable](https://trello.com/c/dwoZ193L) generate a Darwin Core archive file containing taxa, references, and link between taxa and reference.

## Generate Darwin Core Archive from CouchDB

Use list views.

    curl 'http://direct.bionames.org:5984/bionames/_design/darwincorearchive/_list/publication_tsv/publication' > references.tsv

      % Total    % Received % Xferd  Average Speed   Time    Time     Time  Current
                                     Dload  Upload   Total   Spent    Left  Speed
    100 82.3M    0 82.3M    0     0  10507      0 --:--:--  2:16:56 --:--:-- 22208

    curl 'http://direct.bionames.org:5984/bionames/_design/darwincorearchive/_list/taxa_tsv/taxa' > taxa.tsv
    
      % Total    % Received % Xferd  Average Speed   Time    Time     Time  Current
                                     Dload  Upload   Total   Spent    Left  Speed
    100  838M    0  838M    0     0   421k      0 --:--:--  0:33:59 --:--:--  517k    

Create meta.xml by hand, then create archive:

    zip bionames-dwca.zip meta.xml taxa.tsv references.tsv

## Importing data into MySQL

The data in the .tsv files can be imported into MySQL. For example, to import the bibliographic references, in a MySQL database create the following table:

    CREATE TABLE `references` (
    `id` varchar(128) NOT NULL,
    `type` varchar(16) DEFAULT NULL,
    `creator` varchar(255) DEFAULT NULL,
    `title` varchar(255) DEFAULT NULL,
    `year` char(4) DEFAULT NULL,
    `journal` varchar(255) DEFAULT NULL,
    `ISSN` char(9) DEFAULT NULL,
    `oclc` varchar(16) DEFAULT NULL,
    `volume` varchar(16) DEFAULT NULL,
    `issue` varchar(16) DEFAULT NULL,
    `spage` varchar(16) DEFAULT NULL,
    `epage` varchar(16) DEFAULT NULL,
    `URL` varchar(255) DEFAULT NULL,
    `PDF` text,
    `DOI` varchar(255) DEFAULT NULL,
    `Handle` varchar(64) DEFAULT NULL,
    `PMID` varchar(16) DEFAULT NULL,
    `ZooBank` varchar(128) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `title` (`title`),
    KEY `journal` (`journal`),
    KEY `ISSN` (`ISSN`),
    KEY `DOI` (`DOI`),
    KEY `year` (`year`),
    KEY `type` (`type`),
    KEY `creator` (`creator`),
    KEY `oclc` (`oclc`),
    KEY `volume` (`volume`),
    KEY `issue` (`issue`),
    KEY `spage` (`spage`),
    KEY `epage` (`epage`),
    KEY `URL` (`URL`),
    KEY `PDF` (`PDF`(255)),
    KEY `Handle` (`Handle`),
    KEY `PMID` (`PMID`),
    KEY `ZooBank` (`ZooBank`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

One issue with importing tsv/csv data into MySQL is that empty values are not, by default, treated as NULL. Based on this answer in Stack Overflow http://stackoverflow.com/a/5968530 we can do the following (note that this example assumes that the references.tsv file is in the path /Users/rpage, you will need to change this to a folder that is accessible by MySQL on your computer).

    LOAD DATA infile '/Users/rpage/references.tsv'
    INTO TABLE `references`
    FIELDS TERMINATED BY "\t"
    LINES TERMINATED BY "\n"
    IGNORE 1 LINES
    (id,@vtype,@vcreator,@vtitle,@vyear,@vjournal,@vISSN,@voclc,@vvolume,@vissue,@vspage,@vepage,@vURL,@vPDF,@vDOI,@vHandle,@vPMID,@vZooBank)
    SET
    type = nullif(@vtype,''),
    creator = nullif(@vcreator,''),
    title = nullif(@vtitle,''),
    year = nullif(@vyear,''),
    journal = nullif(@vjournal,''),
    ISSN = nullif(@vISSN,''),
    oclc = nullif(@voclc,''),
    volume = nullif(@vvolume,''),
    issue = nullif(@vissue,''),
    spage = nullif(@vspage,''),
    epage = nullif(@vepage,''),
    URL = nullif(@vURL,''),
    PDF = nullif(@vPDF,''),
    DOI = nullif(@vDOI,''),
    Handle = nullif(@vHandle,''),
    PMID = nullif(@vPMID,''),
    ZooBank = nullif(@vZooBank,'')
    ;

