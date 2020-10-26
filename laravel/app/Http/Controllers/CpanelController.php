<?php

namespace App\Http\Controllers;

class CpanelController
{
    protected $user;
    protected $pass;
    protected $domain;
    
    public function __construct()
    {
        $this->user = env('CPANEL_USER');
        $this->pass = env('CPANEL_PASSWORD');
        $this->domain = env('CPANEL_DOMAIN');
    }
    
    public function indexAnalytics()
    {
        return view('panel.analytic.index');
    }
    
    public function iframeAnalytics()
    {
        //it's a .png file...
        if (strpos($_SERVER['QUERY_STRING'], '.png') !== false) {
            $fileQuery = $_SERVER['QUERY_STRING'];
        } //probably first time to access page...
        elseif (empty($_SERVER['QUERY_STRING'])) {
            $fileQuery = "awstats.pl?config=$this->domain&ssl=1&lang=es";
        } //otherwise, all other accesses
        else {
            $fileQuery = 'awstats.pl?'.$_SERVER['QUERY_STRING'];
        }
        
        //now get the file
        $file = $this->getFile($fileQuery);
        
        //check again to see if it was a .png file
        //if it's not, replace the links
        if (strpos($_SERVER['QUERY_STRING'], '.png') === false) {
            $file = str_replace('awstats.pl', basename($_SERVER['PHP_SELF']), $file);
            // $file = str_replace('="/images','="'.basename($_SERVER['PHP_SELF']).'?images',$file);
            $file = str_replace('="/images', '="'.url('images'), $file);
            $file = str_replace('/images/awstats/other/button.gif', url('images/awstats/other/button.gif'), $file);
        } else {
            header("Content-type: image/png");
        }
        
        $file = str_replace('index.php', route('analytics_iframe'), $file);
        
        return $file;
    }
    
    //retrieves the file, either .pl or .png
    protected function getFile($fileQuery)
    {
        //global $user, $pass, $domain;
        // echo "https://$user:$pass@$domain:2083/".$fileQuery;
        return file_get_contents("https://$this->user:$this->pass@$this->domain:2083/".$fileQuery, 'r');
    }
}