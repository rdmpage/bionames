<?php

require_once('bionames-api/lib.php');

// mockup template

// do PHP stuff here to get query parameters...
$id = $_GET['id'];

// OK, we need some HTML content that Google can see when it crawls the page...
$json = get('http://bionames.org/api/id/' . $id);
$obj = null;
$summary_html = '';
if ($json != '')
{
	$obj = json_decode($json);
	
	// simple table dump so Googlebot can see something	
	$summary_html = '';
	$summary_html .= '<table>';
	
	foreach ($obj as $k => $v)
	{
		switch ($k)
		{				
			case 'author':
				foreach ($obj->author as $author)
				{
					$summary_html .= '<tr>';
					$summary_html .= '<td>';
					$summary_html .=  'Author';
					$summary_html .= '</td>';
					$summary_html .= '<td>';
					$summary_html .=  '<a href="authors/' . $author->name . '">' . $author->name . '</a>';
					$summary_html .= '</td>';
					$summary_html .= '</tr>';
				}
				break;
				
			case 'journal':
				foreach ($obj->journal as $jk => $jv)
				{
					switch ($jk)
					{
						case 'identifier':
							foreach ($jv as $identifier)
							{
								switch ($identifier->type)
								{
									case 'issn':
										$summary_html .= '<tr>';
										$summary_html .= '<td>';
										$summary_html .=  'ISSN';
										$summary_html .= '</td>';
										$summary_html .= '<td>';
										$summary_html .=  '<a href="issn/' . $identifier->id . '">' . $identifier->id . '</a>';
										$summary_html .= '</td>';
										$summary_html .= '</tr>';
										break;
										
									default:
										break;
								}
							}
							break;
									
							
						default:
							$summary_html .= '<tr>';
							$summary_html .= '<td>';
							$summary_html .=  $jk;
							$summary_html .= '</td>';
							$summary_html .= '<td>';
							$summary_html .=  $jv;
							$summary_html .= '</td>';
							$summary_html .= '</tr>';								
							break;
					}
				}
				break;	
								
			case 'thumbnail':
				$summary_html .= '<img src="' . $v . '" />';
				break;
				
			default:
				if (is_string($v))
				{
					$summary_html .= '<tr>';
					$summary_html .= '<td>';
					$summary_html .=  $k;
					$summary_html .= '</td>';
					$summary_html .= '<td>';
					$summary_html .=  $v;
					$summary_html .= '</td>';
					$summary_html .= '</tr>';
				}
				break;
		}
	}
	$summary_html .= '</table>';
	
}

?>
<!DOCTYPE html>
<html>
<head>
	<base href="http://bionames.org/" /><!--[if IE]></base><![endif]-->
	<title><?php if ($obj) { echo $obj->title; } else { echo 'Title'; } ?></title>
	
	<!-- standard stuff -->
	<meta charset="utf-8" />
	
	<?php if ($obj) { echo '<meta name="description" content="' . $obj->citation_string . '" />'; } ?>

	<?php require 'stylesheets.inc.php'; ?>
	<?php require 'javascripts.inc.php'; ?>
	<?php require 'uservoice.inc.php'; ?>
	
	<!-- citeproc-js -->
	<script src="vendor/citeproc-js/loadabbrevs.js"></script>
	<script src="vendor/citeproc-js/xmldom.js"></script>
	<script src="vendor/citeproc-js/citeproc.js"></script>
	<script src="vendor/citeproc-js/loadlocale.js"></script>
	<script src="vendor/citeproc-js/loadsys.js"></script>

	<script>	
		var bibdata = null;	
		var cite_styles = new Array();	
		cite_styles['chicago_author_date'] = "<style       xmlns=\"http://purl.org/net/xbiblio/csl\"      class=\"in-text\"   default-locale=\"en-US-x-sort-ja-alalc97-x-sec-en\">  <!-- BOGUS COMMENT -->  <info>    <title>Chicago Manual of Style (Author-Date format)</title>    <id>http://www.zotero.org/styles/chicago-author-date</id>    <link href=\"http://www.zotero.org/styles/chicago-author-date\" />    <author>      <name>Julian Onions</name>      <email>julian.onions@gmail.com</email>    </author>    <category term=\"author-date\" />    <category term=\"generic-base\" />    <updated />    <summary>The author-date variant of the Chicago style</summary>    <link href=\"http://www.chicagomanualofstyle.org/tools_citationguide.html\" rel=\"documentation\" />  </info> <macro name=\"secondary-contributors\">    <choose>      <if match=\"none\" type=\"chapter\">        <group delimiter=\". \">          <choose>            <if variable=\"author\">              <names variable=\"editor\">                <label form=\"verb-short\" prefix=\" \" suffix=\". \" text-case=\"capitalize-first\" />                <name and=\"text\" delimiter=\", \" />              </names>            </if>          </choose>          <choose>            <if match=\"any\" variable=\"author editor\">              <names variable=\"translator\">                <label form=\"verb-short\" prefix=\" \" suffix=\". \" text-case=\"capitalize-first\" />                <name and=\"text\" delimiter=\", \" />              </names>            </if>          </choose>        </group>      </if>    </choose>  </macro><!-- BOGUS COMMENT -->    <macro name=\"container-contributors\">    <choose>      <if type=\"chapter\">        <group delimiter=\", \" prefix=\",\">          <choose>            <if variable=\"author\">              <names variable=\"editor\">                <label form=\"verb-short\" prefix=\" \" suffix=\". \" text-case=\"lowercase\" />                <name and=\"text\" delimiter=\", \" />              </names>            </if>          </choose>          <choose>            <if match=\"any\" variable=\"author editor\">              <names variable=\"translator\">                <label form=\"verb-short\" prefix=\" \" suffix=\". \" text-case=\"lowercase\" />                <name and=\"text\" delimiter=\", \" />              </names>            </if>          </choose>        </group>      </if>    </choose>  </macro>  <macro name=\"anon\">    <choose>      <if match=\"none\" variable=\"author editor translator\">        <text form=\"short\" term=\"anonymous\" text-case=\"capitalize-first\" />      </if>    </choose>  </macro>  <macro name=\"editor\">    <names variable=\"editor\">      <name and=\"text\" delimiter=\", \" delimiter-precedes-last=\"always\" name-as-sort-order=\"first\" sort-separator=\", \" />      <label form=\"short\" prefix=\", \" suffix=\".\" />    </names>  </macro>  <macro name=\"translator\">    <names variable=\"translator\">      <name and=\"text\" delimiter=\", \" delimiter-precedes-last=\"always\" name-as-sort-order=\"first\" sort-separator=\", \" />      <label form=\"verb-short\" prefix=\", \" suffix=\".\" />    </names>  </macro>  <macro name=\"recipient\">    <choose>      <if type=\"personal_communication\">        <choose>          <if variable=\"genre\">            <text text-case=\"capitalize-first\" variable=\"genre\" />          </if>          <else>            <text term=\"letter\" text-case=\"capitalize-first\" />          </else>        </choose>      </if>    </choose>    <names delimiter=\", \" variable=\"recipient\">      <label form=\"verb\" prefix=\" \" suffix=\" \" text-case=\"lowercase\" />      <name and=\"text\" delimiter=\", \" />    </names>  </macro>  <macro name=\"contributors\">    <names variable=\"author\">      <name and=\"text\" delimiter=\", \" delimiter-precedes-last=\"always\" name-as-sort-order=\"first\" sort-separator=\", \" />      <label form=\"verb-short\" prefix=\", \" suffix=\".\" text-case=\"lowercase\" />      <substitute>        <text macro=\"editor\" />        <text macro=\"translator\" />      </substitute>    </names>    <text macro=\"anon\" />    <text macro=\"recipient\" />  </macro>  <macro name=\"contributors-short\">    <names variable=\"author\">      <name and=\"text\" delimiter=\", \" form=\"short\" />      <substitute>        <names variable=\"editor\" />        <names variable=\"translator\" />      </substitute>    </names>    <text macro=\"anon\" />  </macro>  <macro name=\"interviewer\">    <names delimiter=\", \" variable=\"interviewer\">      <label form=\"verb\" prefix=\" \" suffix=\" \" text-case=\"capitalize-first\" />      <name and=\"text\" delimiter=\", \" />    </names>  </macro>  <macro name=\"archive\">    <group delimiter=\". \">      <text text-case=\"capitalize-first\" variable=\"archive_location\" />      <text variable=\"archive\" />      <text variable=\"archive-place\" />    </group>  </macro>  <macro name=\"access\">    <group delimiter=\". \">      <choose>        <if match=\"any\" type=\"graphic report\">          <text macro=\"archive\" />        </if>        <else-if match=\"none\" type=\"book thesis chapter article-journal article-newspaper article-magazine\">          <text macro=\"archive\" />        </else-if>      </choose>      <text prefix=\"doi:\" variable=\"DOI\" />      <text variable=\"URL\" />    </group>  </macro>  <macro name=\"title\">    <!-- BOGUS COMMENT -->  <choose>      <if match=\"none\" variable=\"title\">        <choose>          <if match=\"none\" type=\"personal_communication\">            <text text-case=\"capitalize-first\" variable=\"genre\" />          </if>        </choose>      </if>      <else-if type=\"book\">        <text font-style=\"italic\" variable=\"title\" />      </else-if>      <else>        <text variable=\"title\" />      </else>    </choose>  </macro>  <macro name=\"edition\">    <choose>      <if match=\"any\" type=\"book chapter\">        <choose>          <if is-numeric=\"edition\">            <group delimiter=\" \">              <number form=\"ordinal\" variable=\"edition\" />              <text form=\"short\" suffix=\".\" term=\"edition\" />            </group>          </if>          <else>            <text suffix=\".\" variable=\"edition\" />          </else>        </choose>      </if>    </choose>  </macro>  <macro name=\"locators\">    <choose>      <if type=\"article-journal\">        <text prefix=\" \" variable=\"volume\" />        <text prefix=\", no. \" variable=\"issue\" />      </if>      <else-if type=\"book\">        <group delimiter=\". \" prefix=\". \">          <group>            <text form=\"short\" suffix=\". \" term=\"volume\" text-case=\"capitalize-first\" />            <number form=\"numeric\" variable=\"volume\" />          </group>          <group>            <number form=\"numeric\" variable=\"number-of-volumes\" />            <text form=\"short\" plural=\"true\" prefix=\" \" suffix=\".\" term=\"volume\" />          </group>        </group>      </else-if>    </choose>  </macro>  <macro name=\"locators-chapter\">    <choose>      <if type=\"chapter\">        <group prefix=\", \">          <text suffix=\":\" variable=\"volume\" />          <text variable=\"page\" />        </group>      </if>    </choose>  </macro>  <macro name=\"locators-article\">    <choose>      <if type=\"article-newspaper\">        <group delimiter=\", \" prefix=\", \">          <group>            <text suffix=\" \" variable=\"edition\" />            <text prefix=\" \" term=\"edition\" />          </group>          <group>            <text form=\"short\" suffix=\". \" term=\"section\" />            <text variable=\"section\" />          </group>        </group>      </if>      <else-if type=\"article-journal\">        <text prefix=\": \" variable=\"page\" />      </else-if>    </choose>  </macro>  <macro name=\"point-locators\">    <group>      <choose>        <if locator=\"page\" match=\"none\">          <label form=\"short\" strip-periods=\"false\" suffix=\" \" variable=\"locator\" />        </if>      </choose>      <text variable=\"locator\" />    </group>  </macro>  <macro name=\"container-prefix\">    <text term=\"in\" text-case=\"capitalize-first\" />  </macro>  <macro name=\"container-title\">    <choose>      <if type=\"chapter\">        <text macro=\"container-prefix\" suffix=\" \" />      </if>    </choose>    <text font-style=\"italic\" variable=\"container-title\" form=\"short\"/>  </macro>  <macro name=\"publisher\">    <group delimiter=\": \">      <text variable=\"publisher-place\" />      <text variable=\"publisher\" />    </group>  </macro>  <macro name=\"date\">    <date variable=\"issued\" form=\"text\" date-parts=\"year\"><date-part name=\"year\"/></date>  </macro>  <macro name=\"day-month\">    <date variable=\"issued\">      <date-part name=\"month\" />      <date-part name=\"day\" prefix=\" \" />    </date>  </macro>  <macro name=\"collection-title\">    <text variable=\"collection-title\" />    <text prefix=\" \" variable=\"collection-number\" />  </macro>  <macro name=\"event\">    <group>      <text suffix=\" \" term=\"presented at\" />      <text variable=\"event\" />    </group>  </macro>  <macro name=\"description\">    <group delimiter=\". \">      <text macro=\"interviewer\" />      <text text-case=\"capitalize-first\" variable=\"medium\" />    </group>    <choose>      <if match=\"none\" variable=\"title\"> </if>      <else-if type=\"thesis\"> </else-if>      <else>        <text prefix=\". \" text-case=\"capitalize-first\" variable=\"genre\" />      </else>    </choose>  </macro>  <macro name=\"issue\">    <choose>      <if type=\"article-journal\">        <text macro=\"day-month\" prefix=\" (\" suffix=\")\" />      </if>      <else-if type=\"speech\">        <group delimiter=\", \" prefix=\" \">          <text macro=\"event\" />          <text macro=\"day-month\" />          <text variable=\"event-place\" />        </group>      </else-if>      <else-if match=\"any\" type=\"article-newspaper article-magazine\">        <text macro=\"day-month\" prefix=\", \" />      </else-if>      <else>        <group delimiter=\", \" prefix=\". \">          <choose>            <if type=\"thesis\">              <text text-case=\"capitalize-first\" variable=\"genre\" />            </if>          </choose>          <text macro=\"publisher\" />          <text macro=\"day-month\" />        </group>      </else>    </choose>  </macro>  <citation          disambiguate-add-givenname=\"true\"         disambiguate-add-names=\"true\"         disambiguate-add-year-suffix=\"true\"         et-al-min=\"4\"         et-al-subsequent-min=\"4\"         et-al-subsequent-use-first=\"1\"         et-al-use-first=\"1\">    <layout text-decoration=\"underline\" delimiter=\"; \" prefix=\"(\" suffix=\")\">      <group delimiter=\", \">        <group delimiter=\" \">          <text macro=\"contributors-short\" />          <text macro=\"date\" />        </group>        <text macro=\"point-locators\" />      </group>    </layout>  </citation>  <bibliography          entry-spacing=\"0\"         et-al-min=\"11\"         et-al-use-first=\"7\"         hanging-indent=\"true\"         subsequent-author-substitute=\"---\">    <sort>      <key macro=\"contributors\" />      <key macro=\"date\" />    </sort>    <layout suffix=\".\">      <text macro=\"contributors\" suffix=\". \" />      <text macro=\"date\" suffix=\". \" />      <text macro=\"title\" />      <text macro=\"description\"/>      <text macro=\"secondary-contributors\" prefix=\". \" />      <text macro=\"container-title\" prefix=\". \"/>      <text macro=\"container-contributors\" />      <text macro=\"locators-chapter\" />      <text macro=\"edition\" prefix=\". \" />      <text macro=\"locators\" />      <text macro=\"collection-title\" prefix=\". \" />      <text macro=\"issue\" />      <text macro=\"locators-article\" />      <text macro=\"access\" prefix=\". \" />    </layout>  </bibliography></style>";	
		cite_styles['bibtex'] = "<style xmlns=\"http://purl.org/net/xbiblio/csl\" class=\"in-text\" version=\"1.0\" demote-non-dropping-particle=\"sort-only\">   <info>      <title>BibTex generic citation style</title>      <id>http://www.zotero.org/styles/bibtex</id>      <link href=\"http://www.zotero.org/styles/bibtex\" rel=\"self\"/>      <link href=\"http://www.bibtex.org/\" rel=\"documentation\"/>      <author>         <name>Markus Schaffner</name>      </author>      <contributor>         <name>Richard Karnesky</name>         <email>karnesky+zotero@gmail.com</email>         <uri>http://arc.nucapt.northwestern.edu/Richard_Karnesky</uri>      </contributor>      <category field=\"generic-base\"/>      <updated>2008-10-05T10:04:29-07:00</updated>      <rights>This work is licensed under a Creative Commons Attribution-Share Alike 3.0 License: http://creativecommons.org/licenses/by-sa/3.0/</rights>   </info>   <macro name=\"zotero2bibtexType\">      <choose>         <if type=\"bill book graphic legal_case motion_picture report song\" match=\"any\">            <text value=\"book\"/>         </if>         <else-if type=\"chapter paper-conference\" match=\"any\">            <text value=\"inbook\"/>         </else-if>         <else-if type=\"article article-journal article-magazine article-newspaper\" match=\"any\">            <text value=\"article\"/>         </else-if>         <else-if type=\"thesis\" match=\"any\">            <text value=\"phdthesis\"/>         </else-if>         <else-if type=\"manuscript\" match=\"any\">            <text value=\"unpublished\"/>         </else-if>         <else-if type=\"paper-conference\" match=\"any\">            <text value=\"inproceedings\"/>         </else-if>         <else-if type=\"report\" match=\"any\">            <text value=\"techreport\"/>         </else-if>         <else>            <text value=\"misc\"/>         </else>      </choose>   </macro>   <macro name=\"citeKey\">      <group delimiter=\"_\">         <text macro=\"author-short\" text-case=\"lowercase\"/>         <text macro=\"issued-year\"/>      </group>   </macro>   <macro name=\"author-short\">      <names variable=\"author\">         <name form=\"short\" delimiter=\"_\" delimiter-precedes-last=\"always\"/>         <substitute>            <names variable=\"editor\"/>            <names variable=\"translator\"/>            <choose>               <if type=\"bill book graphic legal_case motion_picture report song\" match=\"any\">                  <text variable=\"title\" form=\"short\"/>               </if>               <else>                  <text variable=\"title\" form=\"short\"/>               </else>            </choose>         </substitute>      </names>   </macro>   <macro name=\"issued-year\">      <date variable=\"issued\">         <date-part name=\"year\"/>      </date>   </macro>   <macro name=\"issued-month\">      <date variable=\"issued\">         <date-part name=\"month\" form=\"short\" strip-periods=\"true\"/>      </date>   </macro>   <macro name=\"author\">      <names variable=\"author\">         <name sort-separator=\", \" delimiter=\" and \" delimiter-precedes-last=\"always\" name-as-sort-order=\"all\"/>         <label form=\"long\" text-case=\"capitalize-first\"/>      </names>   </macro>   <macro name=\"editor-translator\">      <names variable=\"editor translator\" delimiter=\", \">         <name sort-separator=\", \" delimiter=\" and \" delimiter-precedes-last=\"always\" name-as-sort-order=\"all\"/>         <label form=\"long\" text-case=\"capitalize-first\"/>      </names>   </macro>   <macro name=\"title\">      <text variable=\"title\"/>   </macro>   <macro name=\"number\">      <text variable=\"issue\"/>      <text variable=\"number\"/>   </macro>   <macro name=\"container-title\">      <choose>         <if type=\"chapter paper-conference\" match=\"any\">            <text variable=\"container-title\" prefix=\" booktitle={\" suffix=\"}\"/>         </if>         <else>            <text variable=\"container-title\" prefix=\" journal={\" suffix=\"}\"/>         </else>      </choose>   </macro>   <macro name=\"publisher\">      <choose>         <if type=\"thesis\">            <text variable=\"publisher\" prefix=\" school={\" suffix=\"}\"/>         </if>         <else-if type=\"report\">            <text variable=\"publisher\" prefix=\" institution={\" suffix=\"}\"/>         </else-if>         <else>            <text variable=\"publisher\" prefix=\" publisher={\" suffix=\"}\"/>         </else>      </choose>   </macro>   <macro name=\"pages\">      <text variable=\"page\"/>   </macro>   <macro name=\"edition\">      <text variable=\"edition\"/>   </macro>   <citation et-al-min=\"10\" et-al-use-first=\"10\" et-al-subsequent-min=\"10\" et-al-subsequent-use-first=\"10\" disambiguate-add-year-suffix=\"true\" disambiguate-add-names=\"false\" disambiguate-add-givenname=\"false\" collapse=\"year\">      <sort>         <key macro=\"author\"/>         <key variable=\"issued\"/>      </sort>      <layout delimiter=\"_\">         <text macro=\"citeKey\"/>      </layout>   </citation>   <bibliography hanging-indent=\"false\" et-al-min=\"10\" et-al-use-first=\"10\">      <sort>         <key macro=\"author\"/>         <key variable=\"issued\"/>      </sort>      <layout>         <text macro=\"zotero2bibtexType\" prefix=\" @\" suffix=\"\"/>         <group prefix=\"{\" suffix=\"}\" delimiter=\", \">            <text macro=\"citeKey\"/>            <text variable=\"publisher-place\" prefix=\" place={\" suffix=\"}\"/><!--Fix This-->            <text variable=\"chapter-number\" prefix=\" chapter={\" suffix=\"}\"/><!--Fix This-->            <text macro=\"edition\" prefix=\" edition={\" suffix=\"}\"/><!--Is this in CSL? <text variable=\"type\" prefix=\" type={\" suffix=\"}\" />-->            <text variable=\"collection-title\" prefix=\" series={\" suffix=\"}\"/>            <text macro=\"title\" prefix=\" title={\" suffix=\"}\"/>            <text variable=\"volume\" prefix=\" volume={\" suffix=\"}\"/><!--Not in CSL<text variable=\"rights\" prefix=\" rights={\" suffix=\"}\" />-->            <text variable=\"ISBN\" prefix=\" ISBN={\" suffix=\"}\"/><!--Not in CSL <text variable=\"ISSN\" prefix=\" ISSN={\" suffix=\"}\" />--><!--Not in CSL <text variable=\"LCCN\" prefix=\" callNumber={\" suffix=\"}\" />-->            <text variable=\"archive_location\" prefix=\" archiveLocation={\" suffix=\"}\"/>            <text variable=\"URL\" prefix=\" url={\" suffix=\"}\"/>            <text variable=\"DOI\" prefix=\" DOI={\" suffix=\"}\"/>            <text variable=\"abstract\" prefix=\" abstractNote={\" suffix=\"}\"/>            <text variable=\"note\" prefix=\" note={\" suffix=\"}\"/>            <text macro=\"number\" prefix=\" number={\" suffix=\"}\"/>            <text macro=\"container-title\"/>            <text macro=\"publisher\"/>            <text macro=\"author\" prefix=\" author={\" suffix=\"}\"/>            <text macro=\"editor-translator\" prefix=\" editor={\" suffix=\"}\"/>            <text macro=\"issued-year\" prefix=\" year={\" suffix=\"}\"/>            <text macro=\"issued-month\" prefix=\" month={\" suffix=\"}\"/>            <text macro=\"pages\" prefix=\" pages={\" suffix=\"}\"/>            <text variable=\"collection-title\" prefix=\" collection={\" suffix=\"}\"/>         </group>      </layout>   </bibliography></style>";	
		cite_styles['zookeys'] = "<style class=\"in-text\" version=\"1.0\" demote-non-dropping-particle=\"never\">	<info>		<title>			ZooKeys		</title>		<id>			zookeys		</id>		<link href=\"http://www.zotero.org/styles/apa\" rel=\"self\">		</link>		<link href=\"http://www.pensoft.net/journals/zookeys/about/Author%20Guidelines\" rel=\"documentation\">		</link>		<author>			<name>				Roderic D. M. Page			</name>			<email>				rdmpage@gmail.com			</email>			<uri>				http://iphylo.blogspot.com			</uri>		</author>		<category field=\"biology\">		</category>		<category field=\"generic-base\">		</category>		<category citation-format=\"author-date\">		</category>		<!--		<updated>			2010-01-27T20:08:03+00:00		</updated>		-->		<rights>			This work is licensed under a Creative Commons Attribution-ShareAlike 3.0 License: http://creativecommons.org/licenses/by-sa/3.0/		</rights>		<issn>1313-2989</issn>	</info>	<locale lang=\"en\">		<terms>			<term name=\"translator\" form=\"short\">				<single>					trans.				</single>				<multiple>					trans.				</multiple>			</term>		</terms>	</locale>	<macro name=\"container-contributors\">		<choose>			<if type=\"chapter paper-conference\" match=\"any\">				<text term=\"in\" text-case=\"capitalize-first\" suffix=\": \">				</text>				<names variable=\"editor\" delimiter=\", \" suffix=\" \">					<name delimiter=\", \" and=\"\" initialize-with=\"\" sort-separator=\" \" name-as-sort-order=\"all\" >					</name>					<!-- strip-periods=\"true\" removes the '.' after Ed/Eds -->					<label form=\"short\" strip-periods=\"true\" text-case=\"capitalize-first\" prefix=\" (\" suffix=\")\">					</label>					<substitute>						<names variable=\"translator\">						</names>					</substitute>				</names>			</if>		</choose>	</macro>	<macro name=\"secondary-contributors\">		<choose>			<if type=\"chapter paper-conference\" match=\"none\">				<names variable=\"translator\" delimiter=\", \" prefix=\" (\" suffix=\")\">					<name and=\"symbol\" initialize-with=\". \" delimiter=\", \">					</name>					<label form=\"short\" prefix=\", \" text-case=\"capitalize-first\">					</label>					<substitute>						<names variable=\"editor\">						</names>					</substitute>				</names>			</if>		</choose>	</macro>	<macro name=\"author\">		<names variable=\"author\">			<name delimiter=\", \" delimiter-precedes-last=\"always\" initialize-with=\"\" sort-separator=\" \" name-as-sort-order=\"all\" >			</name>			<label form=\"short\" strip-periods=\"true\" text-case=\"capitalize-first\" prefix=\" (\" suffix=\".)\">			</label>			<substitute>				<names variable=\"editor\">				</names>				<names variable=\"translator\">				</names>				<choose>					<if type=\"report\">						<text variable=\"publisher\">						</text>						<text macro=\"title\">						</text>					</if>					<else>						<text macro=\"title\">						</text>					</else>				</choose>			</substitute>		</names>	</macro>	<macro name=\"author-short\">		<names variable=\"author\">			<name form=\"short\" and=\"text\" delimiter=\", \" initialize-with=\". \">			</name>			<substitute>				<names variable=\"editor\">				</names>				<names variable=\"translator\">				</names>				<choose>					<if type=\"report\">						<text variable=\"publisher\">						</text>						<text variable=\"title\" form=\"short\" font-style=\"italic\">						</text>					</if>					<else-if type=\"bill book graphic legal_case motion_picture song\" match=\"any\">						<text variable=\"title\" form=\"short\" font-style=\"italic\">						</text>					</else-if>					<else>						<text variable=\"title\" form=\"short\" quotes=\"false\">						</text>					</else>				</choose>			</substitute>		</names>	</macro>	<macro name=\"access\">		<choose>			<if type=\"thesis\">				<choose>					<if variable=\"archive\" match=\"any\">						<group>							<text term=\"retrieved\" text-case=\"capitalize-first\" suffix=\" \">							</text>							<text term=\"from\" suffix=\" \">							</text>							<text variable=\"archive\" suffix=\".\">							</text>							<text variable=\"archive_location\" prefix=\" (\" suffix=\")\">							</text>						</group>					</if>					<else>						<group>							<text term=\"retrieved\" text-case=\"capitalize-first\" suffix=\" \">							</text>							<text term=\"from\" suffix=\" \">							</text>							<text variable=\"URL\">							</text>						</group>					</else>				</choose>			</if>			<else>				<choose>					<if variable=\"DOI\">						<text variable=\"DOI\" prefix=\"doi: \" suffix=\".\">						</text>					</if>					<else>						<choose>							<if type=\"webpage\">								<group delimiter=\" \">									<!--									<text term=\"retrieved\" text-case=\"capitalize-first\" suffix=\" \">									</text>									<group>										<date variable=\"accessed\" suffix=\", \">											<date-part name=\"month\" suffix=\" \">											</date-part>											<date-part name=\"day\" suffix=\", \">											</date-part>											<date-part name=\"year\">											</date-part>										</date>									</group>									<text term=\"from\">									</text> -->									<text variable=\"URL\">									</text>								</group>							</if>							<else>								<group>									<text term=\"retrieved\" text-case=\"capitalize-first\" suffix=\" \">									</text>									<text term=\"from\" suffix=\" \">									</text>									<text variable=\"URL\">									</text>								</group>							</else>						</choose>					</else>				</choose>			</else>		</choose>	</macro>	<macro name=\"title\">		<choose>			<if type=\"report thesis\" match=\"any\">				<text variable=\"title\">				</text>				<group prefix=\" (\" suffix=\")\">					<text variable=\"genre\">					</text>					<text variable=\"number\" prefix=\" No. \">					</text>				</group>			</if>			<else-if type=\"book graphic  motion_picture report song manuscript speech\" match=\"any\">				<text variable=\"title\" >				</text>			</else-if>			<else>				<text variable=\"title\">				</text>			</else>		</choose>	</macro>	<macro name=\"publisher\">		<choose>			<if type=\"report\" match=\"any\">				<group delimiter=\", \">					<text variable=\"publisher\">					</text>					<text variable=\"publisher-place\">					</text>				</group>			</if>			<else-if type=\"thesis\" match=\"any\">				<group delimiter=\", \">					<text variable=\"publisher\">					</text>					<text variable=\"publisher-place\">					</text>				</group>			</else-if>			<else>				<group delimiter=\", \">					<choose>						<if variable=\"event\" match=\"none\">							<text variable=\"genre\">							</text>						</if>					</choose>					<group delimiter=\", \">						<text variable=\"publisher\">						</text>						<text variable=\"publisher-place\">						</text>					</group>				</group>			</else>		</choose>	</macro>	<macro name=\"event\">		<choose>			<if variable=\"event\">				<choose>					<if variable=\"genre\" match=\"none\">						<text term=\"presented at\" text-case=\"capitalize-first\" suffix=\" \">						</text>						<text variable=\"event\">						</text>					</if>					<else>						<group delimiter=\" \">							<text variable=\"genre\" text-case=\"capitalize-first\">							</text>							<text term=\"presented at\">							</text>							<text variable=\"event\">							</text>						</group>					</else>				</choose>			</if>		</choose>	</macro>	<macro name=\"issued\">		<choose>			<if type=\"legal_case bill\" match=\"none\">				<choose>					<if variable=\"issued\">						<group prefix=\" (\" suffix=\")\">							<date variable=\"issued\">								<date-part name=\"year\">								</date-part>							</date>							<text variable=\"year-suffix\">							</text>							<choose>								<if type=\"bill book graphic legal_case motion_picture report song article-journal chapter paper-conference\" match=\"none\">									<date variable=\"issued\">										<date-part prefix=\", \" name=\"month\">										</date-part>										<date-part prefix=\" \" name=\"day\">										</date-part>									</date>								</if>							</choose>						</group>					</if>					<else>						<if type=\"webpage\">						</if>						<else>												<group prefix=\" (\" suffix=\").\">								<text term=\"no date\" form=\"short\">								</text>								<text variable=\"year-suffix\" prefix=\"-\">								</text>							</group>						</else>					</else>				</choose>			</if>		</choose>	</macro>	<macro name=\"issued-sort\">		<choose>			<if type=\"bill book graphic legal_case motion_picture report song article-journal chapter paper-conference\" match=\"none\">				<date variable=\"issued\">					<date-part name=\"year\">					</date-part>					<date-part prefix=\", \" name=\"month\">					</date-part>					<date-part prefix=\" \" name=\"day\">					</date-part>				</date>			</if>			<else>				<date variable=\"issued\">					<date-part name=\"year\">					</date-part>				</date>			</else>		</choose>	</macro>	<macro name=\"issued-year\">		<choose>			<if variable=\"issued\">				<date variable=\"issued\">					<date-part name=\"year\">					</date-part>				</date>				<text variable=\"year-suffix\">				</text>			</if>			<else>						<if type=\"webpage\">						</if>						<else>																<text term=\"no date\" form=\"short\">				</text>				<text variable=\"year-suffix\" prefix=\"-\">				</text>									</else>			</else>		</choose>	</macro>	<macro name=\"edition\">		<choose>			<if is-numeric=\"edition\">				<group delimiter=\" \">					<number variable=\"edition\" form=\"ordinal\">					</number>					<text term=\"edition\" form=\"short\" suffix=\".\" strip-periods=\"true\">					</text>				</group>			</if>			<else>				<text variable=\"edition\" suffix=\".\">				</text>			</else>		</choose>	</macro>	<macro name=\"locators\">		<choose>			<if type=\"article-journal article-magazine\" match=\"any\">				<group delimiter=\" \" prefix=\" \">					<group suffix=\":\">						<text variable=\"volume\" font-style=\"normal\">						</text>						<text variable=\"issue\" prefix=\"(\" suffix=\")\">						</text>					</group>					<text variable=\"page\">					</text>				</group>			</if>			<else-if type=\"article-newspaper\">				<group delimiter=\" \" prefix=\", \">					<label variable=\"page\" form=\"short\">					</label>					<text variable=\"page\">					</text>				</group>			</else-if>			<else-if type=\"book graphic motion_picture report song chapter paper-conference\" match=\"any\">				<group prefix=\"\" suffix=\" pp\" delimiter=\".\">					<text macro=\"edition\">					</text>					<group>						<text term=\"volume\" form=\"short\" plural=\"true\" text-case=\"capitalize-first\" suffix=\". \" strip-periods=\"true\">						</text>						<number variable=\"number-of-volumes\" form=\"numeric\" prefix=\"1-\">						</number>					</group>					<group>						<text term=\"volume\" form=\"short\" text-case=\"capitalize-first\" suffix=\". \" strip-periods=\"true\">						</text>						<number variable=\"volume\" form=\"numeric\">						</number>					</group>					<group>						<!--<label variable=\"page\" form=\"short\" suffix=\" \">						</label>-->						<text variable=\"page\">						</text>					</group>				</group>			</else-if>			<else-if type=\"legal_case\">				<group prefix=\" (\" suffix=\")\" delimiter=\" \">					<text variable=\"authority\">					</text>					<date variable=\"issued\" delimiter=\" \">						<date-part name=\"month\" form=\"short\">						</date-part>						<date-part name=\"day\" suffix=\",\">						</date-part>						<date-part name=\"year\">						</date-part>					</date>				</group>			</else-if>			<else-if type=\"bill\">				<date variable=\"issued\" prefix=\" (\" suffix=\")\">					<date-part name=\"year\">					</date-part>				</date>			</else-if>		</choose>	</macro>	<macro name=\"citation-locator\">		<group>			<label variable=\"locator\" form=\"short\">			</label>			<text variable=\"locator\" prefix=\" \">			</text>		</group>	</macro>	<macro name=\"container\">		<choose>			<if type=\"legal_case bill\" match=\"none\">				<text variable=\"container-title\" font-style=\"normal\">				</text>			</if>			<else>				<group delimiter=\" \" prefix=\", \">					<choose>						<if variable=\"container-title\">							<text variable=\"volume\">							</text>							<text variable=\"container-title\">							</text>							<group delimiter=\" \">								<text term=\"section\" form=\"symbol\">								</text>								<text variable=\"section\">								</text>							</group>							<text variable=\"page\">							</text>						</if>						<else>							<choose>								<if type=\"legal_case\">									<text variable=\"number\" prefix=\"No. \">									</text>								</if>								<else>									<text variable=\"number\" prefix=\"Pub. L. No. \">									</text>									<group delimiter=\" \">										<text term=\"section\" form=\"symbol\">										</text>										<text variable=\"section\">										</text>									</group>								</else>							</choose>						</else>					</choose>				</group>			</else>		</choose>	</macro>	<citation et-al-min=\"6\" et-al-use-first=\"1\" et-al-subsequent-min=\"3\" et-al-subsequent-use-first=\"1\" disambiguate-add-year-suffix=\"true\" disambiguate-add-names=\"true\" disambiguate-add-givenname=\"true\" collapse=\"year\" givenname-disambiguation-rule=\"primary-name\">		<sort>			<key macro=\"author\">			</key>			<key macro=\"issued-sort\">			</key>		</sort>		<layout prefix=\"(\" suffix=\")\" delimiter=\"; \">			<group delimiter=\" \">				<text macro=\"author-short\">				</text>				<text macro=\"issued-year\">				</text>				<text macro=\"citation-locator\">				</text>			</group>		</layout>	</citation>	<bibliography hanging-indent=\"true\" et-al-min=\"8\" et-al-use-first=\"7\" entry-spacing=\"0\" line-spacing=\"2\">		<sort>			<key macro=\"author\">			</key>			<key macro=\"issued-sort\" sort=\"ascending\">			</key>		</sort>		<layout>			<group suffix=\".\">				<group delimiter=\" \">					<text macro=\"author\">					</text>					<text macro=\"issued\">					</text>				</group>				<group delimiter=\". \">					<text macro=\"title\" prefix=\" \">					</text>					<group>						<text macro=\"container-contributors\">						</text>						<text macro=\"secondary-contributors\">						</text>						<group delimiter=\", \">							<text macro=\"container\">							</text>							<text variable=\"collection-title\">							</text>						</group>					</group>				</group>								<!-- publisher, place -->				<group delimiter=\", \" prefix=\". \" suffix=\", \">					<text macro=\"event\">					</text>					<text macro=\"publisher\">					</text>				</group>								<!-- volume, issue, pagination, DOI -->				<text macro=\"locators\">				</text>							</group>			<text macro=\"access\" prefix=\" \">			</text>		</layout>	</bibliography></style>";	
		cite_styles['zootaxa'] = "<style xmlns=\"http://purl.org/net/xbiblio/csl\" class=\"in-text\" version=\"1.0\" demote-non-dropping-particle=\"sort-only\" default-locale=\"en-US\">  <info>    <title>Zootaxa</title>    <id>http://www.zotero.org/styles/zootaxa</id>    <link href=\"http://www.zotero.org/styles/zootaxa\" rel=\"self\"/>    <link href=\"http://www.mapress.com/zootaxa/support/author.html#Preparation%20of%20manuscripts\" rel=\"documentation\"/>    <link href=\"http://www.zotero.org/styles/apsa\" rel=\"template\"/>    <author>      <name>Bastian Drolshagen</name>      <email>bdrolshagen@gmail.com</email>    </author>    <category citation-format=\"author-date\"/>    <category field=\"biology\"/>    <issn>1175-5326</issn>    <eissn>1175-5334</eissn>    <updated>2012-11-18T18:42:51+00:00</updated>    <summary>The Zootaxa style.</summary>    <rights license=\"http://creativecommons.org/licenses/by-sa/3.0/\">This work is licensed under a Creative Commons Attribution-ShareAlike 3.0 License</rights>  </info>  <locale xml:lang=\"en-US\">    <date form=\"text\">      <date-part name=\"month\" suffix=\" \"/>      <date-part name=\"day\" suffix=\", \"/>      <date-part name=\"year\"/>    </date>  </locale>  <macro name=\"editor\">    <names variable=\"editor\" delimiter=\", \">      <name and=\"text\" initialize-with=\". \" delimiter=\", \"/>      <label form=\"short\" prefix=\" (\" text-case=\"capitalize-first\" suffix=\")\" strip-periods=\"true\"/>    </names>  </macro>  <macro name=\"anon\">    <text term=\"anonymous\" form=\"short\" text-case=\"capitalize-first\" strip-periods=\"true\"/>  </macro>  <macro name=\"author\">    <names variable=\"author\">		<name name-as-sort-order=\"all\" and=\"text\" sort-separator=\", \" initialize-with=\".\" delimiter-precedes-last=\"never\" delimiter=\", \"/>	  <et-al font-style=\"italic\" prefix=\" \"/>      <label form=\"short\" prefix=\" \" suffix=\".\" text-case=\"lowercase\" strip-periods=\"true\"/>      <substitute>        <names variable=\"editor\"/>        <text macro=\"anon\"/>      </substitute>    </names>  </macro>  <macro name=\"author-short\">    <names variable=\"author\">		<name form=\"short\" and=\"symbol\" delimiter=\", \" delimiter-precedes-last=\"never\" initialize-with=\". \"/>		<et-al font-style=\"italic\" prefix=\" \"/>      <substitute>        <names variable=\"editor\"/>        <names variable=\"translator\"/>        <text macro=\"anon\"/>      </substitute>    </names>  </macro>  <macro name=\"access\">    <choose>      <if type=\"legal_case\" match=\"none\">        <choose>          <if variable=\"URL\">            <group delimiter=\" \">              <text variable=\"URL\" prefix=\"Available from: \"/>              <group prefix=\"(\" suffix=\")\">                <date variable=\"accessed\" form=\"text\"/>              </group>            </group>          </if>        </choose>      </if>    </choose>  </macro>  <macro name=\"title\">    <choose>      <if type=\"bill book graphic legal_case legislation motion_picture report song\" match=\"any\">        <text variable=\"title\" font-style=\"italic\"/>      </if>      <else>        <text variable=\"title\" quotes=\"false\"/>      </else>    </choose>  </macro>  <macro name=\"legal_case\">    <group prefix=\" \" delimiter=\" \">      <text variable=\"volume\"/>      <text variable=\"container-title\"/>    </group>    <text variable=\"authority\" prefix=\" (\" suffix=\")\"/>  </macro>  <macro name=\"publisher\">    <choose>      <if type=\"thesis\" match=\"none\">        <group prefix=\"\" suffix=\"\" delimiter=\", \">          <text variable=\"publisher\" suffix=\"\"/>          <text variable=\"publisher-place\"/>        </group>        <text variable=\"genre\" prefix=\". \"/>      </if>      <else>        <group delimiter=\". \">          <text variable=\"genre\"/>          <text variable=\"publisher\"/>        </group>      </else>    </choose>  </macro>  <macro name=\"year-date\">    <choose>      <if variable=\"issued\"><group prefix=\"(\">        <date variable=\"issued\">          <date-part name=\"year\"/>        </date>        </group>      </if>      <else>        <text term=\"no date\" form=\"short\"/>      </else>    </choose>  </macro>  <macro name=\"edition\">    <choose>      <if is-numeric=\"edition\">        <group delimiter=\" \">          <number variable=\"edition\" form=\"ordinal\"/>          <text term=\"edition\" form=\"short\" suffix=\".\" strip-periods=\"true\"/>        </group>      </if>      <else>        <text variable=\"edition\" suffix=\".\"/>      </else>    </choose>  </macro>  <macro name=\"locator\">    <choose>      <if locator=\"page\">        <text variable=\"locator\"/>      </if>      <else>        <group delimiter=\" \">          <label variable=\"locator\" form=\"short\"/>          <text variable=\"locator\"/>        </group>      </else>    </choose>  </macro>  <citation et-al-min=\"2\" et-al-use-first=\"2\" et-al-subsequent-min=\"2\" et-al-subsequent-use-first=\"1\" disambiguate-add-year-suffix=\"true\" disambiguate-add-names=\"true\" disambiguate-add-givenname=\"true\" collapse=\"year\" givenname-disambiguation-rule=\"primary-name\">       <sort>      <key macro=\"author-short\"/>      <key macro=\"year-date\"/>    </sort>    <layout prefix=\"\" delimiter=\"; \">      <group delimiter=\", \">        <group delimiter=\" \">          <text macro=\"author-short\"/><text macro=\"year-date\" suffix=\")\"/>        </group>        <text macro=\"locator\"/>      </group>    </layout>  </citation>  <bibliography hanging-indent=\"true\">    <sort>      <key macro=\"author\"/>      <key macro=\"year-date\"/>      <key variable=\"title\"/>    </sort>    <layout suffix=\" \">      <text macro=\"author\" suffix=\" (\"/>      <date variable=\"issued\">        <date-part name=\"year\" suffix=\")\"/>      </date>      <choose>        <if type=\"book\" match=\"any\">          <text macro=\"legal_case\"/>          <group prefix=\" \" delimiter=\" \">            <text macro=\"title\" suffix=\".\"/>            <text macro=\"edition\"/>            <text macro=\"editor\" suffix=\".\"/>          </group>          <group prefix=\" \" suffix=\".\" delimiter=\", \">          <text macro=\"publisher\"/>          <text variable=\"page\" prefix=\" \" suffix=\" pp\"/>          </group></if>        <else-if type=\"chapter paper-conference\" match=\"any\">                  <text macro=\"title\" prefix=\" \" suffix=\".\"/>          <group prefix=\" In: \" delimiter=\" \"><text macro=\"editor\" suffix=\",\"/>            <text variable=\"container-title\" font-style=\"italic\" suffix=\".\"/>            <text variable=\"collection-title\" suffix=\".\"/>            <group suffix=\".\">              <text macro=\"publisher\"/>              <group prefix=\", pp. \" delimiter=\" \" suffix=\".\">                <text variable=\"page\"/>              </group>            </group>          </group>        </else-if>                        <else-if type=\"bill graphic legal_case legislation manuscript motion_picture report song thesis\" match=\"any\">          <text macro=\"legal_case\"/>          <group prefix=\" \" delimiter=\" \">            <text macro=\"title\" suffix=\".\"/>            <text macro=\"edition\"/>            <text macro=\"editor\" suffix=\".\"/>          </group>          <group prefix=\" \" suffix=\"\" delimiter=\", \">          <text macro=\"publisher\"/>          <text variable=\"page\" prefix=\" \" suffix=\"pp.\"/>          </group>        </else-if>                <else>          <group prefix=\" \" delimiter=\" \" suffix=\".\">            <text macro=\"title\"/>            <text macro=\"editor\"/>          </group>          <group prefix=\" \" suffix=\".\">            <text variable=\"container-title\" font-style=\"italic\"/>            <group prefix=\" \">              <text variable=\"volume\"/>            </group>            <text variable=\"page\" prefix=\", \" suffix=\".\"/>          </group>        </else>              </choose>      <text prefix=\" \" macro=\"access\" suffix=\".\"/>    </layout>  </bibliography></style>";	
	</script>

    <script src="js/bibdata.js"></script>
	
	<!-- documentcloud -->
	<!--[if (!IE)|(gte IE 8)]><!-->
	<link href="public/assets/viewer-datauri.css" media="screen" rel="stylesheet" type="text/css" />
	<link href="public/assets/plain-datauri.css" media="screen" rel="stylesheet" type="text/css" />
	<!--<![endif]-->
	<!--[if lte IE 7]>
	<link href="public/assets/viewer.css" media="screen" rel="stylesheet" type="text/css" />
	<link href="public/assets/plain.css" media="screen" rel="stylesheet" type="text/css" />
	<![endif]-->
	
	<script src="public/assets/viewer.js" type="text/javascript" charset="utf-8"></script>
	<script src="public/assets/templates.js" type="text/javascript" charset="utf-8"></script>
	
	<script> 
		var docUrl = ''; 
		var publication = null;	
	</script>
	
	<!-- altmetric.com -->
	<!-- <script type='text/javascript' src='https://d1bxh8uas1mnw7.cloudfront.net/assets/embed.js'></script> -->
	
	
	
</head>
<body onload="$(window).resize()" class="publication">
	<?php require 'analyticstracking.inc.php'; ?>
	<?php require 'navbar.inc.php'; ?>
	
	<div class="container-fluid" style="margin-top: 60px;">

		<div class="row-fluid">
			<div class="main-content span8">
			
				<ul id="publication-tabs" class="nav nav-tabs">
				  <li class="active"><a href="#view-tab" data-toggle="tab">View</a></li>
				  <li><a href="#details-tab" data-toggle="tab">Details</a></li>
				  <li><a href="#data-tab" data-toggle="tab">Names <span id="data-badge" class="badge badge-info"></span></a></li>
				  <li><a href="#grid-tab" data-toggle="tab">Grid</span></a></li>
				</ul>
			
				<div class="tab-content">				  
				  <div class="tab-pane active" id="view-tab">
					<div id="document-viewer-span">
						<div id="doc">Loading...</div>
					</div>  
				  </div>

				  <div class="tab-pane" id="details-tab">
					<div id="metadata" style="padding:20px;"><?php echo $summary_html; ?></div>  
				  </div>
				  
				  <div class="tab-pane" id="data-tab">
					<div id="names">...</div>
				  </div>

				  <div class="tab-pane" id="grid-tab">
					<div id="grid" style="overflow:auto;">...</div>
				  </div>

				</div>
			</div>
			
			<div class="sidebar span4">
				<div class="sidebar-header">
					<h1 id="title"></h1>
				</div>
				<div id="metadata" class="sidebar-metadata">
					<div id="stats" class="stats"></div>
					<div id="authors" class="sidebar-section"></div>
					<div id="map" class="sidebar-section"></div>	
				</div>
				<div class="sidebar-metadata">
					<div id="view_publisher"></div>
					<div id="view_pdf"></div>
					<div id="view_deepdyve"></div>
					<div id="plugins" class="sidebar-section"></div>	
				</div>
				
				<div>
					<?php require 'disqus.inc.php'; ?>
    			</div>
				
				
			</div>
		</div>
	</div>



<script type="text/javascript">
	var id = "<?php echo $id;?>";
	
	var windowWidth = $(window).width();
	var windowHeight =$(window).height();
	
	function add_metadata_stat(title,value) {
		$(display_stat(title,value)).appendTo($('#stats'));		
	}
	
	
	// Display an object
	function display_publication (id)
	{
		//$.getJSON("http://bionames.org/bionames-api/id/" + id + "?callback=?",
		$.getJSON("api/id/" + id + "?callback=?",
			function(data){
				if (data.status == 200)
				{
					// keep copy of publication data
					publication = data;
					
					bibdata = reference_to_bibdata(data);
					
					// Bibliographic details as a table
					var html = '';
					
					html += '<div id="publication_data">';
					html += '<table class="table">';
					html += '<thead></thead>';
					html += '<tbody>';
					
					// Title of article
					if (data.title)
					{
						html += '<tr><td class="muted">Title</td><td>' + data.title + '</td></tr>';
						$('#title').html(data.title);
						document.title = data.title;
					}
					
					if (data.thumbnail)
					{
						html += '<tr><td class="muted">Thumbnail</td><td><img class="img-polaroid" src="' + data.thumbnail + '" width="100" /></td</tr>';
					}					
										
					if (data.author)
					{
						var sidepanel_html = '<h3>Authors</h3>';
						sidepanel_html += '<ul>';
						for (var j in data.author)
						{
							sidepanel_html += '<li>';
//							sidepanel_html += '<a href="mockup_author.php?name=' + data.author[j].name + '"><i class="icon-user"></i>';
							sidepanel_html += '<a href="authors/' + data.author[j].name + '"><i class="icon-user"></i>';
							sidepanel_html += data.author[j].name + ' ';
							sidepanel_html += '</a>';
							sidepanel_html += '</li>';
						}
						sidepanel_html += '</ul>';
						$("#authors").html(sidepanel_html);
						
						// table row
						html += '<tr><td class="muted">Author(s)</td><td>';
						for (var j in data.author)
						{
//							html += '<a href="mockup_author.php?name=' + data.author[j].name + '"><i class="icon-user"></i>';
							html += '<a href="authors/' + data.author[j].name + '"><i class="icon-user"></i>';
							html += data.author[j].name + ' ';
							html += '</a>';
							html += '  ';
						}
						html += '</td></tr>';
					}
					
					// Journal
					if (data.journal)
					{
						if (data.journal.name)
						{
							html += '<tr><td class="muted">Journal</td><td>' + data.journal.name + '</td></tr>';
							
							// Do we have an ISSN?
							var issn = '';
							var oclc = '';
							if (data.journal.identifier)
							{
								for (var j in data.journal.identifier)
								{
									if (data.journal.identifier[j]) { // kludge, some record may have null identifiers !?
										switch (data.journal.identifier[j].type)
										{
											case 'issn':
												//html += '<tr><td class="muted">ISSN<td><a href="mockup_journal.php?issn=' + data.journal.identifier[j].id + '" rel="tooltip" title="The International Standard Serial Number (ISSN) ' + data.journal.identifier[j].id + ' is a unique identifier for this journal" class="tip">' + data.journal.identifier[j].id + '</a></td></tr>';
												html += '<tr><td class="muted">ISSN<td><a href="issn/' + data.journal.identifier[j].id + '" rel="tooltip" title="The International Standard Serial Number (ISSN) ' + data.journal.identifier[j].id + ' is a unique identifier for this journal" class="tip">' + data.journal.identifier[j].id + '</a></td></tr>';
												break;
	
											case 'oclc':
//												html += '<tr><td class="muted">OCLC</td><td><a href="mockup_journal.php?oclc=' + data.journal.identifier[j].id + '">' + data.journal.identifier[j].id + '</a></td></tr>';
												html += '<tr><td class="muted">OCLC</td><td><a href="oclc/' + data.journal.identifier[j].id + '" rel="tooltip" title="Online Computer Library Center, Inc. (OCLC) number ' + data.journal.identifier[j].id + '" class="tip">' + data.journal.identifier[j].id + '</a></td></tr>';
												break;
												
											default:
												break;
										}
									}
								}
							}
						}
						
						if (data.journal.volume)
						{
							html += '<tr><td class="muted">Volume</td><td>' + data.journal.volume + '</td></tr>';
						}
						if (data.journal.issue)
						{
							html += '<tr><td class="muted">Issue</td><td>' + data.journal.issue + '</td></tr>';
						}
						if (data.journal.pages)
						{
							html += '<tr><td class="muted">Pages</td><td>' + data.journal.pages + '</td></tr>';
						}
					}
					
					// Book
					if (data.book)
					{
						if (data.book.title && (data.type == 'chapter'))
						{
							html += '<tr><td class="muted">Book title</td><td>' + data.book.title + '</td></tr>';
						}
						if (data.book.pages)
						{
							html += '<tr><td class="muted">Pages</td><td>' + data.book.pages + '</td></tr>';
						}
						if (data.book.identifier)
						{
							for (var j in data.book.identifier)
							{
								switch (data.book.identifier[j].type)
								{	
									case "googleBooks":
										html += '<tr><td class="muted">Google Books</td><td>' + '<a href="http://books.google.co.uk/books?id=' + data.book.identifier[j].id + '" target="_new" onClick="_gaq.push([\'_trackEvent\', \'External\', \'googlebooks\', \'' + data.identifier[j].id + '\', 0]);" rel="tooltip" title="This publication is in Google Books" class="tip">' + data.book.identifier[j].id + '</a>' + '</td></tr>';
										break;

									case "isbn":
										html += '<tr><td class="muted">ISBN</td><td>' + '<span rel="tooltip" title="ISBN" class="tip">' + data.book.identifier[j].id + '</span>' + '</td></tr>';
										break;
										
									default:
										break;
								}
							}	
						}
					}
					
					// Item-level identifiers					
					var doi = '';
					
					if (data.identifier)
					{
						for (var j in data.identifier)
						{
							switch (data.identifier[j].type)
							{
								case "ark":
									html += '<tr><td class="muted">ARK</td><td><a href="http://gallica.bnf.fr/ark:/' + data.identifier[j].id + '" target="_new" onClick="_gaq.push([\'_trackEvent\', \'External\', \'gallica\', \'' + data.identifier[j].id + '\', 0]);" rel="tooltip" title="The Archival Resource Key (ARK) ark:/' + data.identifier[j].id + ' is a persistent identifier for this publication" class="tip"><i class="icon-share"></i> ark:/' + data.identifier[j].id + '</a></td></tr>';
									break;

								case "biostor":
									html += '<tr><td class="muted">BioStor</td><td><a href="http://biostor.org/reference/' + data.identifier[j].id + '" target="_new" onClick="_gaq.push([\'_trackEvent\', \'External\', \'biostor\', \'' + data.identifier[j].id + '\', 0]);" rel="tooltip" title="BioStor reference ' + data.identifier[j].id + '" class="tip"><i class="icon-share"></i> ' + data.identifier[j].id + '</a></td></tr>';

									// Display prominent link to BioStor
									plugin_html = '<a href="http://biostor.org/reference/' + data.identifier[j].id + '" target="_new" onClick="_gaq.push([\'_trackEvent\', \'External\', \'biostor\', \'' + data.identifier[j].id + '\', 0]);" class="btn btn-block btn-primary"><i class="icon-share icon-white"></i>View at BioStor</a>';
									$('#view_publisher').html(plugin_html);
									break;

								case "cinii":
									html += '<tr><td class="muted">CiNii</td><td><a href="http://ci.nii.ac.jp/naid/' + data.identifier[j].id + '" target="_new" onClick="_gaq.push([\'_trackEvent\', \'External\', \'cinii\', \'' + data.identifier[j].id + '\', 0]);" rel="tooltip" title="National Institute of Informatics Article ID (NAID)' + data.identifier[j].id + '" class="tip"><i class="icon-share"></i> ' + data.identifier[j].id + '</a></td></tr>';
									break;
									
								case "doi":
									doi = data.identifier[j].id;
									html += '<tr><td class="muted">DOI</td><td><a href="http://dx.doi.org/' + data.identifier[j].id + '" target="_new" onClick="_gaq.push([\'_trackEvent\', \'External\', \'doi\', \'' + data.identifier[j].id + '\', 0]);" rel="tooltip" title="The Digital Object Identifier (DOI) ' + data.identifier[j].id + ' is the persistent identifier for this publication" class="tip"><i class="icon-share"></i> ' + data.identifier[j].id + '</a></td></tr>';
									
									// Display prominent link to publisher
									plugin_html = '<a href="http://dx.doi.org/' + data.identifier[j].id + '" target="_new" onClick="_gaq.push([\'_trackEvent\', \'External\', \'doi\', \'' + data.identifier[j].id + '\', 0]);" class="btn btn-block btn-primary"><i class="icon-share icon-white"></i>View on publisher\'s website</a>';
									$('#view_publisher').html(plugin_html);									
									break;

								case "googleBooks":
									html += '<tr><td class="muted">Google Books</td><td>' + '<a href="http://books.google.co.uk/books?id=' + data.identifier[j].id + '" target="_new" onClick="_gaq.push([\'_trackEvent\', \'External\', \'googlebooks\', \'' + data.identifier[j].id + '\', 0]);" rel="tooltip" title="This publication is in Google Books" class="tip">' + data.identifier[j].id + '</a>' + '</td></tr>';
									break;

								case "handle":
									html += '<tr><td class="muted">Handle</td><td><a href="http://hdl.handle.net/' + data.identifier[j].id + '" target="_new" onClick="_gaq.push([\'_trackEvent\', \'External\', \'handle\', \'' + data.identifier[j].id + '\', 0]);" rel="tooltip" title="The Handle ' + data.identifier[j].id + ' is a persistent identifier for this publication" class="tip"><i class="icon-share"></i> ' + data.identifier[j].id + '</a></td></tr>';
									break;

								case "isbn":
									html += '<tr><td class="muted">ISBN</td><td>' + '<span rel="tooltip" title="ISBN" class="tip">' + data.identifier[j].id + '</span>' + '</td></tr>';
									break;

								case "jstor":
									html += '<tr><td class="muted">JSTOR</td><td><a href="http://www.jstor.org/stable/' + data.identifier[j].id + '" target="_new" onClick="_gaq.push([\'_trackEvent\', \'External\', \'jstor\', \'' + data.identifier[j].id + '\', 0]);" target="_new" rel="tooltip" title="Available from JSTOR" class="tip"><i class="icon-share"></i> ' + data.identifier[j].id + '</a></td></tr>';
									
									// Display prominent link to JSTOR
									plugin_html = '<a href="http://www.jstor.org/stable/' + data.identifier[j].id + '" target="_new" onClick="_gaq.push([\'_trackEvent\', \'External\', \'jstor\', \'' + data.identifier[j].id + '\', 0]);" class="btn btn-block btn-primary"><i class="icon-share icon-white"></i>View at JSTOR</a>';
									$('#view_publisher').html(plugin_html);
									break;

								case "oclc":
									html += '<tr><td class="muted">OCLC</td><td><a href="http://www.worldcat.org/oclc/' + data.identifier[j].id + '" onClick="_gaq.push([\'_trackEvent\', \'External\', \'oclc\', \'' + data.identifier[j].id + '\', 0]);" target="_new" rel="tooltip" title="Online Computer Library Center, Inc. (OCLC) number ' + data.identifier[j].id + '" class="tip"><i class="icon-share"></i> ' + data.identifier[j].id + '</a></td></tr>';
									break;

								case "pmc":
									html += '<tr><td class="muted">PMC</td><td><a href="http://www.ncbi.nlm.nih.gov/pmc/PMC' + data.identifier[j].id + '" target="_new" onClick="_gaq.push([\'_trackEvent\', \'External\', \'pmc\', \'' + data.identifier[j].id + '\', 0]);" rel="tooltip" title="PubMed Central PMC' + data.identifier[j].id + '" class="tip"><i class="icon-share"></i> ' + 'PMC' + data.identifier[j].id + '</a></td></tr>';
									break;

								case "pmid":
									html += '<tr><td class="muted">PMID</td><td><a href="http://www.ncbi.nlm.nih.gov/pubmed/' + data.identifier[j].id + '" target="_new" onClick="_gaq.push([\'_trackEvent\', \'External\', \'pmid\', \'' + data.identifier[j].id + '\', 0]);" rel="tooltip" title="PubMed ID (PMID) ' + data.identifier[j].id + '" class="tip"><i class="icon-share"></i> ' + data.identifier[j].id + '</a></td></tr>';
									break;
									
								default:
									break;
							}
						}	
					}
					
					
					if (doi != '') {
						//var plugin_html = '<h3>Metrics</h3>';;
						//plugin_html += '<div class=\'altmetric-embed\' data-badge-type=\'donut\' data-doi="' + doi + '" data-badge-details=\'right\'></div>';
					
						//$('#plugins').html(plugin_html);
					}
					
					
					// Item-level links
					if (data.link)
					{
						for (var j in data.link)
						{
							switch (data.link[j].anchor)
							{
								case "PDF":
									html += '<tr><td class="muted">PDF</td><td><a href="' + data.link[j].url + '" target="_new" onClick="_gaq.push([\'_trackEvent\', \'External\', \'pdf\', \'' + data.link[j].url + '\', 0]);"><i class="icon-share"></i> ' +  data.link[j].url + '</a></td></tr>';
									
									// Display link to PDF
									plugin_html = '<a href="' + data.link[j].url + '" target="_new" onClick="_gaq.push([\'_trackEvent\', \'External\', \'pdf\', \'' + data.link[j].url + '\', 0]);" class="btn btn-block btn-info"><i class="icon-download-alt icon-white"></i> Download PDF</a>';
									$('#view_pdf').html(plugin_html);
									break;

								case "LINK":
									html += '<tr><td class="muted">URL</td><td><a href="' + data.link[j].url + '" target="_new" onClick="_gaq.push([\'_trackEvent\', \'External\', \'url\', \'' + data.link[j].url + '\', 0]);"><i class="icon-share"></i> ' + data.link[j].url + '</a></td></tr>';
									break;
									
								default:
									break;
							}
						}
					}
					
					if (data.file)
					{
						html += '<tr><td class="muted">SHA1</td><td>' +  data.file.sha1 + '</td></tr>';
					}
					
					// Date
					if (data.year)
					{
						html += '<tr><td class="muted">Year</td><td>' +  data.year + '</td></tr>';
					}
					
					if (data.publisher)
					{
						html += '<tr><td class="muted">Publisher</td><td>' +  data.publisher + '</td></tr>';
					}
					
					html += '<tr><td class="muted">Citation</td>';										
					html += '<td>';
					html += '<select id="format" onchange="show_formatted_citation(this.options[this.selectedIndex].value);"><option label="Format" disabled="disabled" selected="selected"></option><option label="ZooKeys" value="zookeys"><option label="Zootaxa" value="zootaxa"></option><option label="BibTeX" value="bibtex"></option></select>';
					html += '<div>';
					html += '<div id="citation" style="width:400px"></div>';
					html += '</div>';
					html += '</td>';
					html += '</tr>';
					
					html += '<tr><td></td>';
					html += '<td>';
					html += '<span class="Z3988" title="' + referenceToOpenUrl(data) + '"></span>';
					html += '</td>';
					html += '</tr>';
	
					
					/*
					if (doi != '') {
						html += '<tr><td class="muted">Metrics</td><td>' +  '<div class=\'altmetric-embed\' data-badge-type=\'donut\' data-doi="' + doi + '" data-badge-details=\'right\'></div>' + '</td></tr>';
					}
					*/
					
					
					html += '</tbody>';
					html += '</table>';
					
					html += '</div>';
					
					// Citation
					$("#metadata").html(html);
					
					// Map if we have points
					if (data.geometry) {
						html = '<h3>Map</h3>';
						html += '<p class="muted">Localities in publication.</p>';
						html += '<object id="mapsvg" type="image/svg+xml" width="360" height="180" data="map.php?coordinates=' + encodeURIComponent(JSON.stringify(data.geometry.coordinates)) + '"></object>';
						$("#map").html(html);
					}
					
					// Display document viewer if we have a document
					if (data.identifier)
					{
						for (var j in data.identifier)
						{
							//console.log(data.identifier[j].type);
							switch (data.identifier[j].type)
							{
								case "ark":
									// ark:/12148/bpt6k61536173/f400
									
									var ark_pattern = /(.*)\/(.*)\/f(\d+)/;
									var ark = data.identifier[j].id;
									//console.log(ark);
									var match = ark.match(ark_pattern);
									docUrl = 'http://bionames.org/bionames-gallica/documentcloud/' + match[2] + 'f' + match[3] + '.json';
									break;
							
								case "biostor":
									docUrl = 'http://biostor.org/dv/' + data.identifier[j].id + '.json';
									break;
																		
								default:
									break;
							}
						}
					}					
					
					if (docUrl == '')
					{
						if (data.file)
						{
							if (data.file.sha1)
							{
								docUrl = 'http://bionames.org/bionames-archive/documentcloud/' + data.file.sha1 + '.json';
							}
						}
					
					}
					
					display_document();
					
					if (data.title) {
						deep_dyve(data.title);
					}	
					
					display_grid();
					
					$('.tip').tooltip();
				}
			});
	}
	
	function display_document() {
		if (docUrl != '')
		{
			DV.load(docUrl, {
				container: '#doc',
				width:$('#document-viewer-span').width(),
				height:$(window).height() -  $('#document-viewer-span').offset().top,
				//height:$('#document-viewer-span').height(),
				sidebar: false
			});	
		}
		else
		{
			var html = '';
									
			if (publication.thumbnail)
			{							
				html += '<div style="text-align:center;">';
				html += '<div class="alert">';
				html += '<strong>Limited access!</strong> You may need a subscription to access this item.';
				html += '</div>';
				html += '<img style="border:1px solid rgb(128,128,128);padding:10px;background-color:white;" src="' + publication.thumbnail + '" width="400" />';
				html += '</div>';
			}
			else
			{
				html += '<div style="text-align:center;">';
				html += '<div class="alert">';
				html += 'Unable to display this item.';
				html += '</div>';
				html += '</div>';
			}			
				
			$('#doc').html(html);	
		}	
	}
	
	function deep_dyve(title) {
		$.getJSON("http://www.deepdyve.com/openurl?type=jsonp&affiliateId=BioNames&atitle=" + encodeURIComponent(title) + "&callback=?",
			function(data){
				if (data.articleFound) {
					if (data.articleFound == 'true') {
						var html = '';
						//html += '<a href="' + data.articleLink + '" target="_new" onClick="_gaq.push([\'_trackEvent\', \'External\', \'deepdyve\', \'' + data.permId + '\', 0]);"><img src="images/logos/deepdyve_bw.png" /></a>';
						html += '<a href="' + data.articleLink + '" target="_new" onClick="_gaq.push([\'_trackEvent\', \'External\', \'deepdyve\', \'' + data.permId + '\', 0]);" class="btn btn-info btn-block"><i class="icon-share icon-white"></i>View on DeepDyve</a>';
						$('#view_deepdyve').html(html);	
					}
				}
			});
	}
	
	function display_publication_names (id)
	{
		$.getJSON("http://bionames.org/bionames-api/publication/" + id + "/names?callback=?",
			function(data){
				if (data.status == 200)
				{
					add_metadata_stat('Names', data.names.length);	
					
					// Set badge on this tab so people know it has something to see
					$('#data-badge').text(data.names.length);
					// Need this to force tab update
					$('#publication-tabs li:eq(2) a').show();
					
				
					var html = '';
					
					html += '<h4>Names published</h4>';
					
					html += '<table class="table">';
					for (var i in data.names) {
						html += '<tr>';
						html += '<td>';
						//html += '<a href="mockup_taxon_name.php?id=' + data.names[i].cluster + '">';
						html += '<a href="names/' + data.names[i].cluster + '">';
						html += data.names[i].nameComplete;
						
						if (data.names[i].taxonAuthor) {
							html += data.names[i].taxonAuthor;
						}
						
						html += '</a>';
						html += '</td>';
						html += '<td>';
						
						if (data.names[i].id) {
							if (data.names[i].id.match(/urn:lsid:organismnames.com:name:/)) {
								var lsid = 
								html += '<a href="http://www.organismnames.com/details.htm?lsid=' + data.names[i].id.replace('urn:lsid:organismnames.com:name:', '') + '" target="_new" onClick="_gaq.push([\'_trackEvent\', \'External\', \'lsid\', \'' + data.names[i].id + '\', 0]);" rel="tooltip" title="Life Science Identifier (LSID) for this taxon name" class="tip"><i class="icon-share"></i> ' + data.names[i].id + '</a>';
							}
						}
						html += '</td>';
					}
					html += '</table>';
					
					$('#names').html(html);
					
					$('.tip').tooltip();
				}
			});
	}
	
	function display_grid() {
	  if (publication.bhl_pages && publication.names) {
			var html = '';
			html += '<table class="table table-striped table-hover">';
			html += '<thead>';
			
			// Page numbers		
			html += '<tr>';
			html += '<td>';
			html += '</td>';
			html += '<td>';
			
			for (j=0;j<8;j++) {
				html += '<span style="display:block;font-family:monospace;">';
				for (var i in publication.bhl_pages) {
					html += String(publication.bhl_pages[i]).charAt(j);
				   }
				html += '</span>';
			}
			html += '</td>';
			html += '</tr>';
			
			html += '</thead>';
			html += '<tbody>';
			
			if (publication.names)
			for (var j in publication.names) {
				html += '<tr>';
				html += '<td>';
				html += '<a href="search/' + encodeURIComponent(publication.names[j].namestring) + '">';
				html += publication.names[j].namestring;
				html += '</a>';
				html += '</td>';
				html += '<td>';
				
				html += '<span style="font-family:monospace">';
				
				for (var i in publication.bhl_pages) {
				   if (publication.names[j].pages.indexOf(publication.bhl_pages[i]) == -1) {
					html += '&nbsp;';
				   } else {
					html += '●';
				   }
				}
				html += '</span>';
				
				html += '</td>';
				html += '</tr>';
			}
			html += '</tbody>';			
			html += '</table>';
			
			$('#grid').html(html);
		}
	}
					
					
	$("#metadata").html("Object &quot;" + id + "&quot; not found");
		
	display_publication(id);
	display_publication_names(id);
	
	// to do: clicking on tab (e.g. "detail") breaks doc viewer (it will display only a few documant pages)
	// looks like an event gets sent to docviewer that is invalid
	// horrible horrible hack to fix this redisplays the viewer :O 
	$('a[data-toggle="tab"]').on('shown', function (e) {
  
  		var t = $(e.target).text().toLowerCase();
  		if (t == 'view') {
   			//display_publication(id); // horrible
   			display_document();
  		} else {
 			//e.stopImmediatePropagation();
  			//console.log('hi');
  		}
  		
	})	
	
	function show_formatted_citation(format)	{
		
		// This defines the mechanism by which we get hold of the relevant data for
		// the locale and the bibliography. 
		// 
		// In this case, they are pretty trivial, just returning the data which is
		// embedded above. In practice, this might involving retrieving the data from
		// a standard URL, for instance. 
		var sys = {
			retrieveItem: function(id){
				return bibdata[id];
			},
		
			retrieveLocale: function(lang){
				return locale[lang];
			}
		}
					
		// This is the citation object. Here, we have hard-coded this, so it will only
		// work with the correct HTML. 
		var citation_object = 
			{
				// items that are in a citation that we want to add. in this case,
				// there is only one citation object, and we know where it is in
				// advance. 
				"citationItems": [
					{
						"id": "ITEM-1"
					}
				],
				// properties -- count up from 0
				"properties": {
					"noteIndex": 0
				}
				  
			}	
	
		citeproc = new CSL.Engine( sys, cite_styles[format] );
		citeproc.appendCitationCluster( citation_object )[ 0 ][ 1 ];
		$('#citation').html(citeproc.makeBibliography()[ 1 ].join(""));
	}

	// http://stackoverflow.com/questions/6762564/setting-div-width-according-to-the-screen-size-of-user
	$(window).resize(function() { 
		// Only resize document window if we have a document cloud viewer
		if (docUrl) {
			var windowWidth = $('#view-tab').width();
			var windowHeight =$(window).height() -  $('#document-viewer-span').offset().top;
			$('#doc').css({'height':windowHeight, 'width':windowWidth });
		}
	});	

	<!-- typeahead for search box -->
	$("#q").typeahead({
	  source: function (query, process) {
		$.getJSON('http://bionames.org/bionames-api/name/' + query + '/suggestions?callback=?', 
		function (data) {
		  //data = ['Plecopt', 'Peas'];
		  
		  var suggestions = data.suggestions;
		  process(suggestions)
		})
	  }
	})
	
	
	
</script>



</body>
</html>