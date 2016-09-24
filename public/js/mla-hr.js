/**
 * Upload Employee pictures.
 */
function uploadEmployeePictures() {

	var pic_to_upload = [];
	var pic_to_upload_resized = [];

	var pics = $('#employee_upload_pic_form input[type=file]');

	// checking input
	for (var i = 0; i < pics.length; i++) {
		var pic = pics[i];
		var pic_file = pic.files[0];
		// resize pic
		if (typeof pic_file !== "undefined") {

			// Ensure it's an image
			if (pic_file.type.match(/image.*/)) {
				pic_to_upload.push(pic_file);
			}
		}
	}

	if (pic_to_upload.length < 1) {
		$('#modal1').modal();
		return;
	}

	$('#myModal').modal();

	// checking input
	for (var j = 0; j < pic_to_upload.length; j++) {
		var p = pic_to_upload[j];

		console.log(p.size);
		var filetype = p.type;

		// Load the image
		var reader = new FileReader();

		reader.onload = (function(p, pic_to_upload_resized, n) {
			return function(e) {
				var contents = e.target.result;

				// resize
				var image = new Image();

				image.onload = (function(p, pic_to_upload_resized, n) {
					return function(imageEvent) {

						// Resize the image
						var canvas = document.createElement('canvas'), max_size = 1300, // TODO
																						// :
																						// config
						width = image.width, height = image.height;
						if (width > height) {
							if (width > max_size) {
								height *= max_size / width;
								width = max_size;
							}
						} else {
							if (height > max_size) {
								width *= max_size / height;
								height = max_size;
							}
						}
						canvas.width = width;
						canvas.height = height;
						canvas.getContext('2d').drawImage(image, 0, 0, width,
								height);

						switch (filetype) {
						case "image/jpeg":
							var dataUrl = canvas.toDataURL('image/jpeg');
							break;
						case "image/png":
							var dataUrl = canvas.toDataURL('image/png');
							break;
						case "image/bmp":
							var dataUrl = canvas.toDataURL('image/bmp');
							break;
						case "image/jpg":
							var dataUrl = canvas.toDataURL('image/jpeg');
							break;
						default:
							var dataUrl = canvas.toDataURL('image/jpeg');
							break;
						}

						var p_tmp = [];
						p_tmp.push(filetype);
						p_tmp.push(dataUrl);

						pic_to_upload_resized.push(p_tmp);

						isUploadEmployeePicturedCompleted(pic_to_upload_resized, n)
					};
				})(p, pic_to_upload_resized, n);

				image.src = contents;
			};

		})(p, pic_to_upload_resized, pic_to_upload.length);

		reader.readAsDataURL(p);
	}

}

function isUploadEmployeePicturedCompleted(pic_to_upload_resized, n) {
	if (pic_to_upload_resized.length >= n) {

		var employee_id = $("#employee_id").val();
		var redirectUrl = $("#redirectUrl").val();
	
		$.post("/hr/employee/upload-picture1", {
			employee_id : employee_id,
			pictures : pic_to_upload_resized,
		}, function(data, status) {
			window.location = redirectUrl;
		});
	}
}