document.addEventListener("DOMContentLoaded", initGlobe, false);
window.addEventListener("resize", initGlobe, false);

var world;

function initGlobe() {
  var wProp = 1.5;
  var hProp = 1;
  var altitude = 2.5;
  if (document.querySelector("body").clientWidth <= 700) {
    wProp = 1;
    hProp = 1.4;
    altitude = 7.0;
  }

  world = Globe({
    animateIn: true,
  })(document.getElementById("globeContainer"))
    .globeImageUrl("files/images/earth-blue-marble.jpg")
    .bumpImageUrl("files/images/earth-topology.png")
    // .backgroundImageUrl("files/images/night-sky.jpg")
    .backgroundColor("rgba(0,0,0,0)")
    .width(document.querySelector("#globeContainer").offsetWidth * wProp)
    .height(document.querySelector("#globeContainer").offsetHeight * hProp)
    .showGraticules(false)
    .pointOfView(
      { lat: 23.5803171, lng: 58.3232762, altitude: altitude },
      4000
    );

  world.controls().enableZoom = false;
  world.controls().autoRotate = true;
  world.controls().autoRotateSpeed = 0.5 * 1;
  world.controls().enableRotate =
    document.querySelector("body").clientWidth >= 700;

  const CLOUDS_IMG_URL = "files/images/clouds.png";
  const CLOUDS_ALT = 0.004 * 1;
  const CLOUDS_ROTATION_SPEED = -0.006 * 1;

  new THREE.TextureLoader().load(CLOUDS_IMG_URL, (cloudsTexture) => {
    const clouds = new THREE.Mesh(
      new THREE.SphereBufferGeometry(
        world.getGlobeRadius() * (1 + CLOUDS_ALT),
        75,
        75
      ),
      new THREE.MeshPhongMaterial({ map: cloudsTexture, transparent: true })
    );
    world.scene().add(clouds);

    (function rotateClouds() {
      clouds.rotation.y += (CLOUDS_ROTATION_SPEED * Math.PI) / 180;
      requestAnimationFrame(rotateClouds);
    })();
  });

  const globeMaterial = world.globeMaterial();
  globeMaterial.bumpScale = 10 * 1;
  new THREE.TextureLoader().load("files/images/earth-water.png", (texture) => {
    globeMaterial.specularMap = texture;
    globeMaterial.specular = new THREE.Color("grey");
    globeMaterial.shininess = 15 * 1;
  });

  fetch("files/json/omantel-cables.json")
    .then((r) => r.json())
    .then((cablesGeo) => {
      let cablePaths = [];
      cablesGeo.features.forEach(({ geometry, properties }) => {
        geometry.coordinates.forEach((coords) =>
          cablePaths.push({ coords, properties })
        );
      });

      world
        .pathsData(cablePaths)
        .pathPoints("coords")
        .pathPointLat((p) => p[1])
        .pathPointLng((p) => p[0])
        .pathColor((path) => path.properties.color)
        .pathLabel((path) => path.properties.hovertitle)
        .pathDashLength(0.1)
        .pathDashGap(0.008)
        .pathStroke(1)
        .pathDashAnimateTime(12000);
    });

  fetch("files/json/omantel-landing-points.json")
    .then((r) => r.json())
    .then((cablesGeo) => {
      let cablePaths = [];
      cablesGeo.features.forEach(({ geometry, properties }) => {
        properties.type = "omantel-landing-points";
        cablePaths.push({ geometry, properties });
      });
      fetch("files/json/pop-sites.json")
        .then((r) => r.json())
        .then((cablesGeo) => {
          cablesGeo.features.forEach(({ geometry, properties }) => {
            properties.type = "pop-sites";
            cablePaths.push({ geometry, properties });
          });
          world
            .labelsData(cablePaths)
            .labelLat((p) => p.geometry.coordinates[1])
            .labelLng((p) => p.geometry.coordinates[0])
            .labelText(function (path) {
              return path.properties.type == "omantel-landing-points"
                ? ""
                : path.properties.name;
            })
            .labelLabel(function (path) {
              return path.properties.type == "omantel-landing-points"
                ? path.properties.name
                : "";
            })
            .labelDotRadius(function (path) {
              return path.properties.type == "omantel-landing-points"
                ? 0.3
                : 0.2;
            })
            .labelAltitude(function (path) {
              return path.properties.type == "omantel-landing-points"
                ? 0
                : 0.01;
            })
            .labelColor(function (path) {
              return path.properties.type == "omantel-landing-points"
                ? "blue"
                : "red";
            });
        });
    });
}
