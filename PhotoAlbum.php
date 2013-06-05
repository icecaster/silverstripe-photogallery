<?php

class PhotoAlbum extends DataObject { 
	
	static $db = array (
	   	"SortID" => "Int",
		"Name" => "Text",
		"Description" => "HTMLText"
	);
	
	static $has_one = array (
		"PhotoGallery" => "PhotoGallery",
		"Photo" => "Image"
	);
	
	static $has_many = array (
		"PhotoItems" => "PhotoItem"
	);
	
	static $summary_fields = array (
		'Name' => 'Name',
		'DescriptionExcerpt' => 'Description',
		'Thumbnail' => 'Album Cover Photo'
	);

	function canCreate($Member = null) { return true; }
	function canEdit($Member = null) { return true; }
	function canView($Member = null) { return true; }
	function canDelete($Member = null) { return true; }
   
   	public static $default_sort = 'SortID Asc';
   
	public function getCMSFields() {
		$PhotosGridFieldConfig = GridFieldConfig::create()->addComponents(
			new GridFieldToolbarHeader(),
			new GridFieldSortableHeader(),
			new GridFieldDataColumns(),
			new GridFieldPaginator(10),
			new GridFieldEditButton(),
			new GridFieldDeleteAction(),
			new GridFieldDetailForm(),
			new GridFieldBulkEditingTools(),
			new GridFieldBulkImageUpload(),
			new GridFieldSortableRows("SortID")
		);
		$PhotosGridField = new GridField("Photos", "Photo", $this->PhotoItems(), $PhotosGridFieldConfig);
    	$imgfield = UploadField::create('Photo')->setTitle("Gallery Cover Photo");
      	$imgfield->getValidator()->allowedExtensions = array('jpg','jpeg','gif','png');
	  	return new FieldList(
			TextField::create("Name"),
			TextareaField::create("Description"),
			$imgfield,
			$PhotosGridField
		);
	}
	
	public function Thumbnail() {
		$Image = $this->Photo();
		if ( $Image ) 
			return $Image->CMSThumbnail();
		else 
			return null;
	}
	
	function DescriptionExcerpt($length = 75) {
   	$text = strip_tags($this->Description);
   	$length = abs((int)$length);
   	if(strlen($text) > $length) {
   		$text = preg_replace("/^(.{1,$length})(\s.*|$)/s", '\\1 ...', $text);
   	}
   		return $text;
   	}
	
	public function PhotoCropped($x=120,$y=120) {
		 return $this->Photo()->CroppedImage($x,$y);
	}
	
	public function Link() {
        if($PhotoGallery = $this->PhotoGallery()) {
            $Action = 'album/' . $this->ID;
            return $PhotoGallery->Link($Action);   
        }
    }
	
}

?>