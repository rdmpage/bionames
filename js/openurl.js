var c = 0;
function referenceToOpenUrl(reference)
{
	var openurl_keys=[];
	openurl_keys['rft_val_fmt'] = 'rft_val_fmt';
	openurl_keys['jtitle'] 		= 'rft.atitle';
	openurl_keys['title'] 		= 'rft.btitle';
	openurl_keys['authors'] 	= 'rft.au';
	openurl_keys['journal'] 	= 'rft.title';
	openurl_keys['volume'] 		= 'rft.volume';
	openurl_keys['issue'] 		= 'rft.issue';
	openurl_keys['spage'] 		= 'rft.spage';
	openurl_keys['epage'] 		= 'rft.epage';
	openurl_keys['year'] 		= 'rft.date';	
	openurl_keys['issn'] 		= 'rft.issn';	
	openurl_keys['rft_id'] 		= 'rft_id';	
	
	var parameters=[];
	parameters.push('url_ver=Z39.88-2004');
	var delimiter = '&';
	
	for (property in reference)
	{
		switch (property)
		{				
			case 'title':
				// what kind of reference?
				if (reference.journal)
				{
					parameters.push(openurl_keys['jtitle'] + '=' + encodeURIComponent(reference[property]));
				}
				else
				{
					parameters.push(openurl_keys['btitle'] + '=' + encodeURIComponent(reference[property]));
				}
				break;
				
			case 'identifier':
				for (j in reference.identifier)
				{
					switch (reference.identifier[j].type)
					{
						case 'biostor':
							parameters.push(openurl_keys['rft_id'] + '=' + 'http://biostor.org/reference/' + reference.identifier[j].id);
							break;

						case 'doi':
							parameters.push(openurl_keys['rft_id'] + '=' + 'info:doi/' + reference.identifier[j].id);
							break;

						case 'handle':
							parameters.push(openurl_keys['rft_id'] + '=' + 'info:hdl/' + reference.identifier[j].id);
							break;

						case 'pmid':
							parameters.push(openurl_keys['rft_id'] + '=' + 'info:pmid/' + reference.identifier[j].id);
							break;
							
						default:
							break;
					}
				}
				break;
				
				
			case 'journal':
				for (p in reference.journal)
				{
					switch(p)
					{
						case 'name':
							parameters.push(openurl_keys['journal'] + '=' + reference.journal[p]);
							break;
							
						case 'pages':
            				var startingPage = reference.journal.pages;
            				var endingPage = null;
              				var pagePelimiter = reference.journal.pages.indexOf('-');
              				if (pagePelimiter != -1)
              				{
              					startingPage = reference.journal.pages.substring(0, pagePelimiter);
              					endingPage = reference.journal.pages.substring(pagePelimiter+2);
               				}
               				parameters.push(openurl_keys['spage'] + '=' + startingPage);
               				if (endingPage)
               				{
               					parameters.push(openurl_keys['epage'] + '=' + endingPage);
               				} 						
							break;
							
						case 'identifier':
							for (j in reference.journal.identifier)
							{
								if (reference.journal.identifier[j] && reference.journal.identifier[j].type == 'issn')
								{
									parameters.push(openurl_keys['issn'] + '=' + reference.journal.identifier[j].id);
								}
							}
							break;
							
						default:
							if (p in openurl_keys)
							{
								parameters.push(openurl_keys[p] + '=' + reference.journal[p]);
							}
							break;
					}
				}				
				break;
				
			default:
				if (property in openurl_keys)
				{
					parameters.push(openurl_keys[property] + '=' + reference[property]);
				}
				break;
		}
	}

  
	var openurl = parameters.join(delimiter);

	return openurl;
}