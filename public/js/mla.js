$(document).ready(function(){
	$(document).ready(function() {
        $("#lightgallery").lightGallery(); 
        countdown();
    });
		
});

/**
 *	function on calendar dialog
 */
function showDialog()
{
	q =  $( "#asset" ).val();	
	
	$("#dialog").html('Please wait ...');
		$.ajax({
		  url: "/inventory/search/asset?json=1&query=" + q,
		  
		  success: function(text){
			  	var obj = eval(text	);
				var n_hits = obj.length;
				//alert(n_hits);
				//var html = "No asset found"
				var s;
				var i;
			    s="";
				if(n_hits > 0){
					s = '<table class="pure-table pure-table-bordered"><thead><tr><td>ID</td><td>NAME</td><td>TAG</td><td>ACTION</td><td>DETAIL</td></thead></tr>';
					for(i=0; i< n_hits; i++)
					{
					s = s + "<tr>"	
						var id  = obj[i]["id"];
						var name = obj[i]["name"];
						var tag = obj[i]["tag"];
					s = s + '<td>' +  id + '</td>';
					s = s + '<td>' +  name + '</td>';
					s = s + '<td>' +  tag	 + '</td>';
					s = s + '<td><a href="javascript:;" onclick="selectAsset(\''+ id + '\',\''+ name + '\',\''+ tag +'\')">  Select  </a></td>';
					s = s + '<td><a href="/inventory/asset/show?id='+ id + '" target="_blank">  Detail  </a></td>';
				s = s + "</tr>";
						
					}
					s = s + "</table>";

				}else{
					
					s="No asset found!";
				}
					
				//alert(s);
				
				$("#dialog").html(s);
		  }
	});
	
	
	//$( "#dialog" ).text(t);
	$( "#dialog" ).dialog({width :860,height:500,title: "Select asset", modal: true, dialogClass: 'dialogClass'});
}

function selectAsset(id,name,tag){
	$( "#asset_id" ).val(id);
	$( "#asset" ).val(tag);
	$( "#dialog" ).dialog("close");
	
}


/**
 *	function on calendar dialog
 */
function checkSparePartCode()
{
	q =  $( "#sparepart_code" ).val();	
	
	$("#dialog").html('Please wait ...');
		$.ajax({
		  url: "/inventory/search/sparepart?json=1&query=" + q,
		  
		  success: function(text){
			  	var obj = eval(text	);
				var n_hits = obj.length;
				//alert(n_hits);
				//var html = "No asset found"
				var s;
				var i;
			    s="";
				if(n_hits > 0){
					
					s= 'Sparepart code:<b>"' + q + '"</b> exits already. Please recheck!<hr>';
					
					s = s+ '<table <table class="pure-table pure-table-bordered"><thead><tr><td>ID</td><td>NAME</td><td>TAG</td><td>CODE</td><td>DETAIL</td></thead></tr>';
					for(i=0; i< n_hits; i++)
					{
					s = s + "<tr>"	
						var id  = obj[i]["id"];
						var name = obj[i]["name"];
						var tag = obj[i]["tag"];
						var code = obj[i]["code"];
					s = s + '<td>' +  id + '</td>';
					s = s + '<td>' +  name + '</td>';
					s = s + '<td>' +  tag	 + '</td>';
					s = s + '<td>' +  code	 + '</td>';
					//s = s + '<td><a href="javascript:;" onclick="selectAsset(\''+ id + '\',\''+ name + '\',\''+ tag +'\')">  SELECT  </a></td>';
					s = s + '<td><a href="/inventory/spareparts/show?id='+ id + '" target="_blank">  Detail  </a></td>';

						
					s = s + "</tr>";
						
					}
					s = s + "</table>";

				}else{
					
					s= 'Sparepart code:<b>"' + q + '"</b> can be used<hr>';
					
				}
					
				//alert(s);
				
				$("#dialog").html(s);
		  }
	});
	
	
	//$( "#dialog" ).text(t);
	$( "#dialog" ).dialog({width :650,height:500,title: "Check Sparepart Code", modal: true, dialogClass: 'dialogClass'});
}

/**
 * 
 * 
 * 
 */
function uploadPictures(){
	
	var pic_to_upload = [];
	
	
	var pics  = $('#UploadPicsForm input[type=file]');
	
		
	for (var i = 0; i < pics.length; i++) {
		
		var pic = pics[i];
		var pic_file =  pic.files[0];
		
		// resize pic		
		if (typeof pic_file !== "undefined") {
			
				// Ensure it's an image
		    if(pic_file.type.match(/image.*/)) {
		    	
		    	console.log(pic_file.size);
		    	var filetype = pic_file.type;
		    	
		        // Load the image
			    var reader = new FileReader();
		       
			    reader.onload = function (e) {
			    	
			        var image = new Image();
			        image.scr = reader.result;
			        image.onload = function (imageEvent) {
				        	
			        	// Resize the image
		                var canvas = document.createElement('canvas'),
		                    max_size = 1500,// TODO : pull max size from a site config
		                    width = image.width,
		                    height = image.height;
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
		                canvas.getContext('2d').drawImage(image, 0, 0, width, height);
		                
		                
			            switch(filetype) {
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
		                
		                var asset_id=  $( "#asset_id" ).val();
		                
		                $.post("/inventory/asset/upload-picture1",
		                        {
		                          pictures:  dataUrl,
		                          id: asset_id,
		                          filetype: filetype,
		                        },
		                        function(data,status){
		                            alert("Picture was uploaded \nStatus: " + status);
		                            $('#UploadPicsForm input[type=file]').val('').clone(true);
		                            
		                });
		                
			        }
		            image.src = reader.result;
		        }
		        
		        reader.readAsDataURL(pic_file);
                
  		    }
		}
	}
}


/**
 * 
 * 
 * 
 */
function countAsset(){
	
	
	
	var pic_to_upload = [];
	var pic_to_upload_resized = [];
	
	var pics  = $('#CountAssetForm input[type=file]');
	
	// checking input
	for (var i = 0; i < pics.length; i++) {
		var pic = pics[i];
		var pic_file =  pic.files[0];
		// resize pic		
		if (typeof pic_file !== "undefined") {
			
				// Ensure it's an image
		    if(pic_file.type.match(/image.*/)) {
		    	pic_to_upload.push(pic_file);
		    }
		}
	}
	
	if(pic_to_upload.length<1){
		
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
	    	
	    reader.onload = (function(p, pic_to_upload_resized,n) {
            return function(e) {
                var contents = e.target.result;
                
                 //resize
                var image = new Image();
		        
		        image.onload =  (function(p, pic_to_upload_resized,n){
			        return function (imageEvent) {
			        	
			        	// Resize the image
		                var canvas = document.createElement('canvas'),
		                    max_size = 1200,// TODO : pull max size from a site config
		                    width = image.width,
		                    height = image.height;
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
		                canvas.getContext('2d').drawImage(image, 0, 0, width, height);
			            
		                switch(filetype) {
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
			            
			            isCountingCompleted(pic_to_upload_resized,n)
	                };
            	})(p, pic_to_upload_resized,n);
		        
                image.src = contents;
             };
             
	    		
             
        })(p, pic_to_upload_resized,pic_to_upload.length);
	    
        reader.readAsDataURL(p);
	}

}

function isCountingCompleted(pic_to_upload_resized,n)
{
	if(pic_to_upload_resized.length>=n){
		
		var asset_id=  $( "#asset_id" ).val();
		var counting_id=  $( "#counting_id" ).val();
		var updated_location=  $( "#updated_location" ).val();
		var redirectUrl=  $( "#redirectUrl" ).val();
		
			//alert(pic_to_upload_resized.length);
       
        $.post("/inventory/count/add-counting-item1",
                {
            	  asset_id: asset_id,
            	  counting_id: counting_id,
            	  updated_location: updated_location,
                  pictures:  pic_to_upload_resized,
                },
                function(data,status){
                       window.location = redirectUrl;
        });
	}
	
}


/*
 * 
 * 
 */
function uploadAssetPictures(){
	
	 
	
	var pic_to_upload = [];
	var pic_to_upload_resized = [];
	
	var pics  = $('#UploadPicsForm input[type=file]');
	
	// checking input
	for (var i = 0; i < pics.length; i++) {
		var pic = pics[i];
		var pic_file =  pic.files[0];
		// resize pic		
		if (typeof pic_file !== "undefined") {
			
				// Ensure it's an image
		    if(pic_file.type.match(/image.*/)) {
		    	pic_to_upload.push(pic_file);
		    }
		}
	}
	
	if(pic_to_upload.length<1){
		
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
	    	
	    reader.onload = (function(p, pic_to_upload_resized,n) {
           return function(e) {
               var contents = e.target.result;
                 
               //resize
               var image = new Image();
		        
		        image.onload =  (function(p, pic_to_upload_resized,n){
			        return function (imageEvent) {
			        	
			        	// Resize the image
		                var canvas = document.createElement('canvas'),
		                    max_size = 1200,// TODO : pull max size from a site config
		                    width = image.width,
		                    height = image.height;
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
		                canvas.getContext('2d').drawImage(image, 0, 0, width, height);
		                
		                
			            switch(filetype) {
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
			            
			            isUploadAssetPicturedCompleted(pic_to_upload_resized,n)
	                };
           	})(p, pic_to_upload_resized,n);
		        
               image.src = contents;
            };
            
	    		
            
       })(p, pic_to_upload_resized,pic_to_upload.length);
	    
       reader.readAsDataURL(p);
	}

}

function isUploadAssetPicturedCompleted(pic_to_upload_resized,n)
{
	if(pic_to_upload_resized.length>=n){
		
		var asset_id=  $( "#asset_id" ).val();
		var redirectUrl=  $( "#redirectUrl" ).val();
	      
       $.post("/inventory/asset/upload-picture1",
               {
    	   			asset_id: asset_id,
    	   			pictures:  pic_to_upload_resized,
               },
               function(data,status){
                      window.location = redirectUrl;
       });
	}
	
}


