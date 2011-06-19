=== MyHeb ===
Contributors: mark.veltzer@gmail.com
Tags: myheb, hebrew, multi language, rtl
Requires at least: 3.0
Tested up to: 3.1.3
Stable tag: 0.0.3

MyHeb plugin to WordPress.

== Description ==

This plugin adapts post headings and post content to the
hebrew language, reversing them. This allows you to manage
a blog where some of the posts are in hebrew and some of the
posts are in a different language (usually English).

Currently the implementation is quite simple - if the content
or the post header is in hebrew then they will be displayed in RTL,
otherwise they will be left alone.

What this plugin does not do:
- it does not handle languages other than hebrew (maybe it will in the future).
- it is not smart at the moment - it will reverse any piece of text that has hebrew
in it so mixed posts will be turned into RTL.

Ideas for future improvement:
- each post will have a tag that tells this plugin whether to reverse it or not.

== Installation ==

Upload the MyHeb plugin to your blog, Activate it.

These is no configuration panel at the moment.

== Changelog ==

0.0.1

* separated this plugin into it's own space

0.0.2

* the plugin is now self contained.

0.0.3

* removed cruft
