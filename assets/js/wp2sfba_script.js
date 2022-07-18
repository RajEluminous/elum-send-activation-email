
window.fbAsyncInit = function() {
	console.log('------wp2sfba_ajax.fbappid-------');
	//$('#userData').hide();
	//console.log(wp2sfba_ajax.afterlogin_redirect_to);
    // FB JavaScript SDK configuration and setup
    FB.init({
      appId      : parseInt(wp2sfba_ajax.fbappid), // FB App ID
      cookie     : true,  // enable cookies to allow the server to access the session
      xfbml      : true,  // parse social plugins on this page
      version    : 'v3.0' // use graph api version 2.8
    });
    	
    // Check whether the user already logged in
	FB.getLoginStatus(function(response) {
		if (response.status === 'connected') {
			console.log('Connected...');
			//display user data
			getFbUserData();
		} else {
			$('#status').hide();
			$('#userData').hide();
			$('#fbLink').show();
		}
	});
};

// Load the JavaScript SDK asynchronously
(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

// Facebook login with JavaScript SDK
function fbLogin() {
	 
	console.log("1");
    FB.login(function (response) {
		console.log("2");
        if (response.authResponse) {
			console.log("3");
            // Get and display the user profile data
            getFbUserData();
        } else {
			console.log("4");
            document.getElementById('status').innerHTML = 'User cancelled login or did not fully authorize.';
        }
    }, {scope: 'email'});
	console.log("5");
}

// Fetch the user profile data from facebook
function getFbUserData(){
    FB.api('/me', {locale: 'en_US', fields: 'id,first_name,last_name,email,link,gender,locale,picture'},
    function (response) {
		var res = response;
		$('#status').show();
		$('#fbLink').show();
		/*
		document.getElementById('wp2sfba_fb_profile_img').setAttribute("src",response.picture.data.url);
		document.getElementById('wp2sfba_fb_profile_name').innerHTML = response.first_name+' '+response.last_name;	
		document.getElementById('wp2sfbatitle').innerHTML = response.email;  
		document.getElementById('wp2sfba_fb_profile_logout').setAttribute("onclick","fbLogout()");
		*/				
		document.getElementById('fbLink').setAttribute("class","fbLogoutCls");
        document.getElementById('fbLink').setAttribute("onclick","fbLogout()");
        document.getElementById('fbLink').innerHTML = 'Logout from Facebook';
        //document.getElementById('status').innerHTML = 'Thanks for logging in, ' + response.first_name + '!';
        document.getElementById('status').innerHTML = '';
		document.getElementById('userData').innerHTML = '<p><img style="border-radius: 90%;" src="'+response.picture.data.url+'"/></p><p><h2 id="wp2sfba_fb_profile_name">'+response.first_name+' '+response.last_name+'</h2></p><p><b>'+response.email+'</b></p><p><button id="btnSaveUserInfo" class="btnSaveUserInfo violet">Yes this is me !</button></p><p>&nbsp;</p>';
		
		//document.getElementById('btnSaveUserInfo').setAttribute("onclick",wp2sfba_redirectusr());
				
		/*
		document.getElementById('userData').innerHTML = '<p><b>FB ID:</b> '+response.id+'</p><p><b>Name:</b> '+response.first_name+' '+response.last_name+'</p><p><b>Email:</b> '+response.email+'</p><p><b>Gender:</b> '+response.gender+'</p><p><b>Locale:</b> '+response.locale+'</p><p><b>Picture:</b> <img src="'+response.picture.data.url+'"/></p><p><b>FB Profile:</b> <a target="_blank" href="'+response.link+'">click to view profile</a></p>';
		*/
		
		document.getElementById("btnSaveUserInfo").addEventListener("click", wp2sfba_processUserData);
		$('#userData').show();
		// Save user data
		// wp2sfba_saveUserData(response);
    });
}
 


function wp2sfba_processUserData() {
	
	// show loader
	var loadurl = wp2sfba_ajax.abs_url+'/assets/load.gif'	
	 document.getElementById('status').innerHTML = '<img src="'+loadurl+'" />';
     FB.api('/me', {locale: 'en_US', fields: 'id,first_name,last_name,email,link,gender,locale,picture'},
    function (response) {
		wp2sfba_saveUserData(response);
	 });	
}

function wp2sfba_redirectusr(){
	console.log('---response---');
	 
	// window.location.href=wp2sfba_ajax.afterlogin_redirect_to;
}
// Make ajax call and save user data to the database
// After saving redirect to admin home page.
function wp2sfba_saveUserData(userData){
    console.log('------wp2sfba_saveUserData-------');
	console.log(wp2sfba_ajax.fbappid);
	//window.location.href = wp2sfba_ajax.afterlogin_redirect_to;
	 
	$('#successFBnotice').hide();
	$('#errorFBnotice').hide(); 	 
	jQuery.ajax({
		type: 'POST',
		dataType: 'json',
		url: wp2sfba_ajax.ajaxurl,
		data: {
			action: 'wp2sfba_ajx_save_fbuser_info',
			userData: JSON.stringify(userData) 
		},
		success:function(data, textStatus, XMLHttpRequest){
			document.getElementById('status').innerHTML = '';
			if(data.status=='error') {
				fbLogout();
				$('#fbLink').hide();
				$('#errorFBnotice').html(''+data.message+'');
				$('#errorFBnotice').show();
			}
			if(data.status=='success') {
				console.log('--success--redirect it --');
				window.location.href = wp2sfba_ajax.afterlogin_redirect_to;
			}
			console.log(data); 	
		},
		error: function(MLHttpRequest, textStatus, errorThrown){
			//resetForm();
			document.getElementById('status').innerHTML = '';
			alert(errorThrown);
		}
	});	
	 
}

// Logout from facebook
function fbLogout() {
    FB.logout(function() {
		
		$('#userData').hide();
		document.getElementById('status').setAttribute("class","fbLogoutCls");
        document.getElementById('fbLink').setAttribute("onclick","fbLogin()");
        document.getElementById('fbLink').innerHTML = '<button class="loginBtn loginBtn--facebook">Connect with Facebook</button>';
        document.getElementById('userData').innerHTML = '';
        document.getElementById('status').innerHTML = 'You have successfully logout from Facebook.';
		$('#fbLink').show();
    });
}
