[![DOI](https://zenodo.org/badge/DOI/10.5281/zenodo.4584110.svg)](https://doi.org/10.5281/zenodo.4584110)

> [!WARNING]  
> As of 2024 Zenodo has updated their system to directly provide IIIF manifests and resources for images within a given dataset. So I will archive this repo.
> 
> As an example for the following dataset: Fort, M., & Gibson, A. (2022). Image Data sets for use in Heritage Science. Zenodo. https://doi.org/10.5281/zenodo.7319696
> There is a corresponding IIIF manifest at: https://zenodo.org/api/iiif/record:7319696/manifest
> The dataset page in Zenodo now shows a trimmed down version of Mirador V3, rather than their old simple viewer, showing the images and providing access to the relevant metadata.
>


> [!WARNING]  
> This version of the IIIF Zenodo has been updated to attempt to work with the current version of Zenodo, but there seems to be an issue with the info.json files produced by Zenodo so the generated manifests will not resolve in IIIF viewers.
> 
# iiif-zenodo

This is a quick experiment in how one can automatically generate a [IIIF](https://iiif.io) manifest for images stored on Zenodo.

It has worked for a few tested examples but only as an initial proof of concept and has not had any detailed error managment added in.

A working example of the code has been setup at: https://cima.ng-london.org.uk/zenodo/ - if a Zenodo ID, for an image, is added to the end of the URL, such as: https://cima.ng-london.org.uk/zenodo/3758523 a simple image manifest will be returned.

See: [old example manifest](example-manifest.json) - not from current code, and also may now longer open correctly due to changes on Zenodo

The manifest can be loaded into any public IIIF viewer such as:
* https://mirador-dev.netlify.app/__tests__/integration/mirador/
* https://jpadfield.github.io/simple-mirador/Standard%20Example.html

## Acknowledgement
This specific project was prepared and tested as part of:

### The AHRC Funded [IIIF - TANC](https://tanc-ahrc.github.io/IIIF-TNC) project
<img height="64px" src="https://github.com/jpadfield/simple-site/blob/master/docs/graphics/TANC - IIIF.png" alt="IIIF - TNC">
