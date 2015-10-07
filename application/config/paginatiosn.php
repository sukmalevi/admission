/* The index() function gets called on page load. */
	public function index()
	{
    /* Get the page number from the URI (/index.php/pagination/index/{pageid}) */
    $page = $this->uri->segment(3);
    
    /* Load the 'pagination' library */
    $this->load->library('pagination');
    
    /* Set the config parameters */
    $config['base_url'] = 'http://a9a7e44dad07.runnable.com/' + '/index.php/pagination/index/';
    $config['total_rows'] = count($this->names);
    $config['per_page'] = 10; 
    
    /* Initialize the pagination library with the config array */
    $this->pagination->initialize($config);
    
    
    /* Call the function (see below) to get a segmented portion of the $names array */
    $pagedNames = $this->get_names($page);
    
    
    /* Setup the variables that will be passed to the view */
    $data = array(
              'names'=> $pagedNames, 
              'pages'=> $this->pagination->create_links()
            );
    
    /* Load the view and pass the variables */
		$this->load->view('page_view', $data);
	}
    
  
  /* The function used to retrieve and format (via list items) 
     a subset of names from the $names array */
  function get_names($offset=0)
  {
    /* Clear the return variable */
    $return = "";
    
    /* Ensure we don't retrieve more names from the array than the number that is available
       when accounting for the $offset passed. */
    $count = count($this->names)-$offset;
    $num = ($count > 9) ? 10 : $count; 
    
    /* Collect the users in the $return variable after surrounding with <li> tags */
    for ($i=0; $i<$num; $i++) 
    {
      $return .= '<li>';
      $return .= $this->names[$i+$offset];
      $return .= '</li>';
    }
    
    /* Return the names */
    return $return;
  }

  
}

/* End of file pagination.php */
/* Location: ./application/controllers/pagination.php */