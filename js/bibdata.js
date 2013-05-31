function reference_to_bibdata(reference) {

	var item = [];
	item['id'] = 'ITEM-1';
	
	if (reference.title) {
		item['title'] = reference.title;
	}
	
	if (reference.journal) {
		item['type'] = 'article-journal';
		if (reference.journal.name) {
			item['container-title'] = reference.journal.name;
		}
		if (reference.journal.volume) {
			item['volume'] = reference.journal.volume;
		}
		if (reference.journal.issue) {
			item['issue'] = reference.journal.issue;
		}
		if (reference.journal.pages) {
			item['page'] = reference.journal.pages.replace(/--/, '-');
		}
	}
	
	if (reference.author)
	{
		item['author'] = [];
		for (var j in reference.author)
		{
			var author = [];
			if (reference.author[j].firstname) {
				author.given = reference.author[j].firstname;
				author.family = reference.author[j].lastname;
			} else {
				author.literal = reference.author[j].name;
			}
			item['author'].push(author);
		}
	}
	
	if (reference.year) {
		item['issued'] = { 'date-parts': [[reference.year]] };		
	}
	
	if (reference.identifier)
	{
		for (var j in reference.identifier)
		{
			switch (reference.identifier[j].type)
			{
				case 'doi':
					item['DOI'] = reference.identifier[j].id;
					break;
					
				default:
					break;
			}
		}
	}	
	
	var bibdata = new Array();
	bibdata['ITEM-1'] = item;
		
	return bibdata;
}