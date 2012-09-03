##Coffee Script impress.js implementation with timer and vote extensions ##

This templates extends the article concept of https://github.com/mo-gr/wcig4d.git .
Every presentation slide is treated as an article.
The vote extension allows to collect listeners' opinions while presenting.
The timer extension helps both the speaker and the audience to estimate the progress of the presentation. 

## article-based presentation concept ##

In mo-gr's presentation concept every slide is an article. If you speak in terms of website presentation style an article may be a blog post. Using the term article as an abstract item name broadens the contexts in which you may use this template.

mo-gr's presentation lib is written in CoffeeScript. Thus, you need to compile it before you can use it:

    $ coffee -c impress.coffee

By default, articles are optimized in slide format for Safari 5.1.4. They should also work in a recent Chrome, but probably make trouble on non-webkit browsers. More about CoffeeScript and mo-gr's presentation: visit https://github.com/mo-gr/wcig4d .

## a template to create your own time limited HTML5 presentations with integrated voting ##
1. checkout this repo
2. run coffee -c js/impress.coffee
3. include your contents in index.html
4. present, publish, whatever

### use the time limit extension ###
Every article, which shall be time limited, needs to contain the following: 
<article class="slide"> 
<span class="timer"></span>
[...]
</article>

### using the voting extension ###
To be able to vote each person needs an account i.e. username-password-combination. This is managed through a file called auth.conf
By default it is placed in a folder parallely to the www-root folder. You can change the path to the file in auth_configuration.inc.
The file contains lines following the scheme "username password timestampOfPasswordCreation".

To add a voting section to an article just add the class name withvoting. The vote.js does the rest for you.
<article class="slide withvoting">
[...]
</article>


