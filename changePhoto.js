$(document).ready(function(){
	//Global variables
	var arr = []; 
	var obj = [];
	var threadStatus = true;
	var sec = 0;
	var repeat = null;


	$('#submit').click(function getData(){
		var xhttp = new XMLHttpRequest();

		//Gets the inputed username from the form
		var usernames = $('#bar').val();
		//Gets the boolean value from the repeat checkbox
		repeat = $('#repeat_status').is(':checked');

		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200){
				var len = arr.length;
				//If the original array of IG photo srcs is empty, clone arr2 into arr
				if(len == 0){
					arr = JSON.parse(this.responseText).url;
					obj = JSON.parse(this.responseText).obj;
				}
				//else append the array into athe origianl array
				else{
					arr = arr.concat(JSON.parse(this.responseText).url);
					obj = obj.concat(JSON.parse(this.responseText).obj);
				}

				//The threadStatus helps prevent multiple intervals from executing (limits to 1)
				if(threadStatus){
					//Create a seperate interval to run the photo swapping
					var thread = setInterval(function(){
						//If the index is on the last photo, stop the interval or repeat
						if(arr.length == sec){
							if(repeat){
								sec = 0;
							} else{
								clearInterval(thread);
								threadStatus = true;
								--sec;
							}
						}
						//Changed the image the the next one in the array
						$('#insta_pic').attr('src',arr[sec]);
						//$('#text').text(obj[sec]);
						++sec; 
					}, 1000);
				threadStatus = false;
				}
			}
		}

		//Pass the IG username to the PHP file
		xhttp.open("GET","backend/get_AccInstaInfo.php?user="+usernames);
		xhttp.send();
	});

	$('#get_JSON').click(function(){
		var JSON_arr = {'url':arr,'obj':obj};
		var open = window.open("");
		/*JSON array has url and obj --> JSONOBJECT.url or .obj
		 *the indexes correspond*/
		open.document.writeln(JSON.stringify(JSON_arr));
	});
});