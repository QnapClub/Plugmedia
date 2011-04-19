<?

class CORE_SortingOrdering {

	// Sorting, ordering values
	private $ordering = '';			//ASC or DESC
	private $sorting = '';			// sorting: name, date, ...
	
	
	public function CORE_SortingOrdering()
	{
		global $SESSION;
		
		if (array_key_exists('order',$_POST))
			$this->ordering = $this->sanitizeOrdering($_POST['order']);
		else
		{
			if ($SESSION->getData('order'))
				$this->ordering = $this->sanitizeOrdering($SESSION->getData('order'));
			else
				$this->ordering = $this->sanitizeOrdering('');
		}
		
		if (array_key_exists('tris',$_POST))
			$this->sorting = $this->sanitizeSorting($_POST['tris']);
		else
		{
			if ($SESSION->getData('tris'))
				$this->sorting = $this->sanitizeSorting($SESSION->getData('tris'));
			else
				$this->sorting = $this->sanitizeSorting('');
		
		}
		
	}
	
	
	public function setSortingOrdering($sorting, $ordering)
	{
		if ($ordering!=false)
			$this->ordering = $this->sanitizeOrdering($ordering);	
		if ($sorting!=false)
			$this->sorting = $this->sanitizeSorting($sorting);
	}
	

	
	public function extraOrdering ($is_file=true)
	{
		switch ($this->sorting)
		{
			case 'N': if ($is_file) return 'ORDER BY filename '.$this->ordering; else return 'ORDER BY dir.parent '.$this->ordering.',dir.name '.$this->ordering ; break;
			case 'D': if ($is_file) return 'ORDER BY timestamp_modification '.$this->ordering; else return 'ORDER BY dir.original_date '.$this->ordering; break;
			case 'S': if ($is_file) return 'ORDER BY filesize '.$this->ordering; else  return '';  break;
		}
	}
	
	public function getOrdering()
	{
		return $this->ordering;
	}
	
	public function getSorting()
	{
		return $this->sorting;
	}	
		
	
	
	private function sanitizeOrdering($order)
	{
		switch ($order)
		{
			case 'ASC': return $order; break;
			case 'DESC': return $order; break;
			default: return 'ASC';
		}
	}

	private function sanitizeSorting($sort)
	{
		// tris sur le nom, la date, la size
		switch ($sort)
		{
			case 'N': return $sort; break;
			case 'D': return $sort; break;
			case 'S': return $sort; break;
			case 'M': return $sort; break;
			default: return 'N';
		}
	}

}


?>