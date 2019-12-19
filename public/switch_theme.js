$('#switch_theme').on('change', () => {
    if ($('#switch_theme').prop('checked')) {
        $('#theme').val('light')
        $('#dark').replaceWith("<link rel=\"stylesheet\" id=\"light\" href=\"https://bootswatch.com/4/litera/bootstrap.min.css\">")
    } else {
        $('#theme').val('dark')
        $('#light').replaceWith("<link rel=\"stylesheet\" id=\"dark\" href=\"https://bootswatch.com/4/cyborg/bootstrap.min.css\">")
    }
    $.ajax('/changeTheme/' + $('#theme').val()).done(function (res) {
        console.log(res)
    });
    $('h1').toggleClass("display-4 text-dark")
    $('.navbar').toggleClass("bg-primary bg-dark")
    $('.card').toggleClass("bg-secondary")
    $('.jumbotron').toggleClass("bg-white")
    $('#view_jumbotron').toggleClass("bg-white")
    $('.card-header h1').toggleClass("text-dark text-white")
})

$(document).ready(function() {
    if ($('#theme').val() == 'dark') {
        $('h1').toggleClass("display-4 text-dark")
        $('.navbar').toggleClass("bg-primary bg-dark")
        $('.card').toggleClass("bg-secondary")
        $('.jumbotron').toggleClass("bg-white")
        $('#view_jumbotron').toggleClass("bg-white")
        $('.card-header h1').toggleClass("text-dark text-white")
    }
})