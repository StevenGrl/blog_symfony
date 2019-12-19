$(document).ready(function() {
    $.ajax('/countArticles').done(function (res) {
        $('#nb_articles_mine').append(res.mine)
        $('#nb_favourite_articles').append(res.favourite)
    });
})