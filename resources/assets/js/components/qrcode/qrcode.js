$(document).ready(function () {
    $('button#qrscan').click(function () {
        console.log(uploaded_image_url);
        if(uploaded_image_url != 'https://htch.us:4433/images/' || uploaded_image_url != "") {
            $.ajax({
                type: "get",
                url: 'qrcode/scan',
                data: {image_url: uploaded_image_url},
                success: function (data) {
                    console.log(data);
                    if (data.success == 'true'){
                    	alert("ok");
                    	$('.product_link').hide();
                    	$('form.dropzone').append('<a class="product_link" href="' + data.product_url + '" style="float: right;">Go to Product-></a>');
                    } else {
						alert("no ok");
						$('form.dropzone').append('<h5>Not a Product!</h5>')
                    }
                }
            });
        }
    });
});