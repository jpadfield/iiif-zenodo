# iiif-zenodo

This is a quick experiment in how one can automatically generate a [IIIF](https://iiif.io) manifest for images stored on Zenodo.

It has worked for a few tested examples but only as an initial proof of concept and has not had any detailed error managment added in.

A working example of the code has been setup at: https://cima.ng-london.org.uk/zenodo/ - if a Zenodo ID, for an image, is added to the end of the URL, such as: https://cima.ng-london.org.uk/zenodo/3758523 a simple image manifest will be returned.

See: [example manifest](example-manifest.json)

The manifest can be loaded into any public IIIF viewer such as:
* https://mirador-dev.netlify.app/__tests__/integration/mirador/
* https://jpadfield.github.io/simple-mirador/Standard%20Example.html

