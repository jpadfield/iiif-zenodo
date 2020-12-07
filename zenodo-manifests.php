<?php

if (!$_GET["id"] or $_GET["id"] == "index.html" )
	{$id = "https://zenodo.org/record/1434056";}
else
	{$id = "https://zenodo.org/record/".$_GET["id"];}
		
$dets = getZenodoJsonDetails ($id, true);

$arr = array(
	"id" => $dets["doi"],
	"thumb" => $dets["links"]["thumb250"],
	"description" => json_encode($dets["metadata"]["description"]),
	"title" => json_encode($dets["metadata"]["title"]),
	"notes" => json_encode($dets["metadata"]["notes"]),
	"license" => $dets["metadata"]["license"]["id"],
	"creators" => $dets["metadata"]["creators"],
	"type" => $dets["metadata"]["resource_type"]["type"]
	);

$arr = addImageDets ($arr);	
$man = buildManifest($arr);

if ($man)
	{header('Content-Type: application/json');
	 echo $man;}
else
	{http_response_code(403);}

function buildManifest($arr)
	{
	$attrib = "";
	foreach ($arr["creators"] as $k => $ca)
		{$arr["creators"][$k] = implode(",", $ca);}
	$attrib = json_encode(trim(implode(",", $arr["creators"]), ","));
	
	
	echo <<<END
{  
   "@context":"http://iiif.io/api/presentation/2/context.json",
   "@id":"$arr[id]",
   "@type":"sc:Manifest",
   "label":$arr[title],
   "license":"$arr[license]", 
   "attribution":$attrib,
   "logo": "https://about.zenodo.org/static/img/logos/zenodo-gradient-200.png",
   "metadata":[  
      {  
         "label":"Image Description",
         "value":$arr[description]
      }
   ],
   "description":$arr[notes],
   "viewingDirection":"left-to-right",
   "viewingHint":"individuals",
   "sequences":[  
      {  
         "@id":"/zenodo/manifests/sequence/normal.json",
         "@type":"sc:Sequence",
         "label":"Normal Order",
         "canvases":[  
END;

$h = $arr["info"]["height"];
$w = $arr["info"]["width"];
$id = $arr["info"]["@id"];

			echo <<<END

		{  
               "@id":"https://cima.ng-london.org.uk/zenodo/manifests/sequence/$arr[id]/normal.json",
               "@type":"sc:Canvas",
               "label":$arr[title],
               "height":$h,
               "width":$w,
               "images":[  
                  {  
                     "@type":"oa:Annotation",
                     "motivation":"sc:painting",
                     "resource":{  
                        "@id":"$id/full/full/0/default.jpg",
                        "@type":"dctypes:Image",
                        "format":"image/jpeg",
                        "height":$h,
                        "width":$w,
                        "service":{  
                           "@context":"http://iiif.io/api/image/2/context.json",
                           "@id":"$id",
                           "profile":"http://iiif.io/api/image/2/level2.json"
                        }
                     },
                     "on":"https://cima.ng-london.org.uk/zenodo/manifests/sequence/$arr[id]/normal.json"
                  }
               ]
            }
END;
         
	echo <<<END
         ]
      }
   ]
}

END;

	$json = ob_get_contents();
	ob_end_clean(); // Don't send output to client	

	return ($json);
	}
	

function addImageDets ($arr)
	{
	if (preg_match("/^(.+)([\/]full[\/].+default[.]jpg)$/", $arr["thumb"], $m))
		{$info = $m[1]."/info.json";
		 $arr["info"] = getRemoteJsonDetails ($info, false, true);}
		 
	return ($arr);
	}
	
function getRemoteJsonDetails ($uri, $format=false, $decode=false)
	{if ($format) {$uri = $uri.".".$format;}
	 $fc = file_get_contents($uri);
	 if ($decode)
		{$output = json_decode($fc, true);}
	 else
		{$output = $fc;}
	 return ($output);}

function getZenodoJsonDetails ($id, $decode=false)
	{
	$fc = file_get_contents($id."/export/json");
	$fc = explode("\n", $fc);
	$json = "";
	$echo = false;
	 
	foreach ($fc as $k => $line)
		{
		if ($line == "<pre style=\"white-space: pre-wrap;\">{")
			{$line = "{";
			 $echo = true;}
		else if ($line == "}</pre>")
			{$echo = false;
			 $json .= "}";}
		
		if ($echo)
			{$json .= $line."\n";}
		}
		
	$json = htmlspecialchars_decode($json);
	 
	if ($decode)
		{$output = json_decode($json, true);}
	else
		{$output = $json;}
		
	return ($output);
	}
	 
?>

