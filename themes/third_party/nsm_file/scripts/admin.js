(function($) {

	$.fn.NSM_File = function(options) {

		var opts = $.extend({}, $.fn.NSM_File.defaults, options);

		return this.each(function() {

			var $self = $(this), dom = {};
			field_id = $self.attr('data-field_id');

			dom.$field = $self;

			dom.$addTrigger = $('.nsm_file-add', $self);
			dom.$removeTrigger = $('.remove-file', $self);

			dom.$fileAttributes = $('> div:gt(0)', $self);
			dom.$filePreview = $('.nsm_file-preview', $self);
			dom.$fileDirInput = $('input[name*=\[filedir\]]', $self);
			dom.$fileNameInput = $('input[name*=\[filename\]]', $self);
			dom.$fileImageInput = $('input[name*=\[is_image\]]', $self);
			
			$.ee_filebrowser.add_trigger(dom.$addTrigger, field_id, function(file, field) {

				dom.$fileDirInput.val(file.directory);
				dom.$fileNameInput.val(file.name);
				dom.$fileImageInput.val(file.is_image);
				
				$img = $('<img />').attr('src', file.thumb);
				$a = $('<a />').attr({
						'href' : file.thumb.split('_thumbs\/thumb_')[0] + file.name,
						'target' : '_blank'
					}).append($img, file.name);
				dom.$filePreview.prepend($a);
				
				 dom.$addTrigger.hide();
				 dom.$fileAttributes.show();

				return false;

			});

			dom.$removeTrigger.click(function(){
				$('a', dom.$filePreview).remove();
				dom.$fileAttributes.hide();
				dom.$addTrigger.show();
				return false;
			});

			if($self.attr('data-filename') == ''){
				dom.$fileAttributes.hide();
				dom.$addTrigger.show();
			} else {
				dom.$addTrigger.hide();
				dom.$fileAttributes.show();
			}
			
			

		});
	}

	if(typeof(Matrix)=='undefined')
	{
		$('.nsm_file_ft').NSM_File();
	}
	else
	{
		Matrix.bind('nsm_file', 'display', function(cell){
			$('.nsm_file_ft', cell.dom.$td).NSM_File();
		});
	}

})(jQuery);


