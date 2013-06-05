<?php

class PhotoItem extends DataObject { 
	
	static $db = array (
	   	"SortID" => "Int",
		"Caption" => "Text"
	);
	
	static $has_one = array (
		"PhotoGallery" => "PhotoGallery",
		"PhotoAlbum" => "PhotoAlbum",
		"Photo" => "Image"
	);

	function canCreate($Member = null) { return true; }
	function canEdit($Member = null) { return true; }
	function canView($Member = null) { return true; }
	function canDelete($Member = null) { return true; }
	
	public static $default_sort = 'SortID Asc';
	
	public function getCMSFields() {
		$imgfield = UploadField::create('Photo');
      	$imgfield->getValidator()->allowedExtensions = array('jpg','jpeg','gif','png');
		return new FieldList(
		   	$imgfield,
			TextField::create('Caption')
		);
	}
	
	static $summary_fields = array (
      	'CaptionExcerpt' => 'Caption',
      	'Thumbnail' => 'Photo'
   	);
	
	public function Thumbnail() {
		$Image = $this->Photo();
		if ( $Image ) 
			return $Image->CMSThumbnail();
		else 
			return null;
	}
	
	function CaptionExcerpt($length = 75) {
		$text = strip_tags($this->Caption);
		$length = abs((int)$length);
		if(strlen($text) > $length) {
			$text = preg_replace("/^(.{1,$length})(\s.*|$)/s", '\\1 ...', $text);
		}
		return $text;
	}
	
	public function getAlbums() {
		$albums = PhotoAlbum::get()->sort('Created DESC');
		if($albums->Exists()) {
		 	return $albums->map('ID', 'Name', 'Please Select');
		}
		else { 
			return array('No albums found');
		}
	}
	
	public function PhotoCropped($x=120,$y=120) {
		return $this->Photo()->CroppedImage($x,$y);
	}
	
	public function PhotoSized($x=700,$y=700) {
		return $this->Photo()->SetRatioSize($x,$y);
	}
	
}

?>