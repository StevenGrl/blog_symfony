$(document).ready(function() {
    let heart = $('.fa-heart')
    $(heart).on('click', function() {
        if (heart.attr('class').match('far')) {
            heart.removeClass('far').addClass('fas').toggleClass('fa-lg fa-sm')
            $.ajax('/fr/blog/like/article/' + $('#idArticle').val()).done(function (res) {
                console.log(res)
            });
        } else {
            heart.removeClass('fas').addClass('far').toggleClass('fa-sm fa-lg')
            $.get('/fr/blog/unlike/article/' + $('#idArticle').val()).done(function (res) {
                console.log(res)
            });
        }
    })
})