Themes
======

How to create a theme for Miniflux?
-----------------------------------

It's very easy to write a custom theme for Miniflux.

A theme is just a CSS file, images and fonts.
A theme doesn't change the behaviour of the application but only the page layout.

The first step is to create a new directory structure for your theme:

```bash
mkdir -p themes/mysuperskin/{css,img,fonts}
```

The name of your theme should be only alphanumeric.

There is the following directories inside your theme:

- `css`: Your stylesheet, the file must be named `app.css` (required)
- `img`: Theme images (not required)
- `fonts`: Theme fonts (not required)

Miniflux use responsive design, so it's better if your theme can handle mobile devices.

List of themes
--------------

Since the version 1.1.7, themes are not anymore distributed in the default installation.

You can download them from [the official Miniflux repositories](https://github.com/miniflux):

- [Bootstrap Light by Silvus](https://github.com/miniflux/theme-bootstrap-light)
- [Bootswatch Cyborg by Silvus](https://github.com/miniflux/theme-bootswatch-cyborg)
- [Cards by Augustin Lacour](https://github.com/miniflux/theme-cards)
- [Copper by Nicolas Dewaele](https://github.com/miniflux/theme-copper)
- [Green by Maxime](https://github.com/miniflux/theme-green)
- [Hello by Mirado ZAKASOA](https://github.com/meradoou/hello)
- [Hello Flat by Mirado ZAKASOA](https://github.com/meradoou/hello/tree/flat)
- [Midnight by Luca Marra](https://github.com/miniflux/theme-midnight)
- [NoStyle by Frederic Guillot](https://github.com/miniflux/theme-nostyle)
- [Still by Franklin Delehelle](https://github.com/miniflux/theme-still)
- [Sun by Alexander Mangel](https://github.com/cygnusfear/miniflux-theme-sun)
- [EvenMoreMinimalist by Lacereation](https://github.com/lacereation/minflux-theme)

**PS: Those themes are maintained and tested by their respective authors.**
