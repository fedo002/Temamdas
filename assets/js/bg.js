// background.js
document.addEventListener('DOMContentLoaded', function() {
    // Three.js ayarları
    var scene = new THREE.Scene();
    scene.fog = new THREE.Fog(0x000000, 10, 15);
    var camera = new THREE.PerspectiveCamera(45, window.innerWidth/window.innerHeight, 0.1, 1000);

    var renderer = new THREE.WebGLRenderer({ alpha: true }); // Şeffaf arka plan için
    renderer.setSize(window.innerWidth, window.innerHeight);
    renderer.domElement.style.position = 'fixed';
    renderer.domElement.style.top = '0';
    renderer.domElement.style.left = '0';
    renderer.domElement.style.zIndex = '-1'; // İçeriğin arkasında olması için
    document.body.insertBefore(renderer.domElement, document.body.firstChild);
    
    // Renk fonksiyonları
    function randint(min, max) {
      return Math.floor(Math.random() * max) + min
    }
    
    function RGB(r, g, b) {
      function colorcheck(c) {
        if (c > 255) {
          c = 255
        }
        if (c < 0) {
          c = 0
        }
        return c
      }
      r = colorcheck(r)
      g = colorcheck(g)
      b = colorcheck(b)
      return 'rgb(' + r + ',' + g + ',' + b + ')'
    }
    
    function rgb2hex(rgb){
     rgb = rgb.match(/^rgba?[\s+]?\([\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?/i);
     return (rgb && rgb.length === 4) ? "0x" +
      ("0" + parseInt(rgb[1],10).toString(16)).slice(-2) +
      ("0" + parseInt(rgb[2],10).toString(16)).slice(-2) +
      ("0" + parseInt(rgb[3],10).toString(16)).slice(-2) : '';
    }
    
    function rgb2color(r, g, b) {
      return rgb2hex(RGB(r, g, b))
    }
    
    function sin(t, h) {
      return Math.sin(t) * h
    }
    
    // DigiMineX teması için renk ayarları (mavi tonları)
    var primaryColor = 0x3a86ff; // --primary-color
    var secondaryColor = 0x0d47a1; // --secondary-color
    
    // Küpleri oluştur
    var cubes = [];
    for (var x = -6; x <= 6; x++) {
      for (var z = -6; z <= 6; z++) {
        var cube = [];
        var geometry = new THREE.BoxGeometry(1, 1, 1, 6, 6, 6);
        var smooth = geometry.clone();
        
        if (Math.random() > 0.75) {
          var ccolor = primaryColor; // Mavi renkli küpler için primary renginizi kullanın
          cube.colored = true;
        } else {
          var ccolor = 'black';
          cube.colored = false;
        }
        
        cube.material = new THREE.MeshPhongMaterial({ color: ccolor });
        cube.mesh = new THREE.Mesh(smooth, cube.material);
        scene.add(cube.mesh);
        cube.mesh.position.x = x;
        cube.mesh.position.z = z;
        cube.height = randint(1,10)/10;
        cube.aniOffset = randint(1,400)/100;
        cubes.push(cube);
      }
    }
    
    // Işıklar ekle
    for (var x = -5; x <= 5; x+= 5) {
      for (var z = -5; z <= 5; z+= 5) {
        var light = new THREE.PointLight('white', 1, 7.5);
        light.position.set(x, 2, z);
        scene.add(light);
      }
    }
    
    // Kamera pozisyonu
    camera.position.y = 8;
    camera.position.x = 6;
    camera.position.z = 4;
    var lookAt = new THREE.Vector3(0,0,0);
    camera.lookAt(lookAt);
    
    // Renk değişim animasyonu
    var color = {};
    color.r = 0;
    color.g = 0;
    color.b = 255;
    color.rs = 0;
    color.gs = 0;
    color.bs = 0;
    color.rt = 0;
    color.gt = 0;
    color.bt = 255;
    
    var time = 0;
    
    function mainloop() {
      time += 1;
      
      // Kamera hareketleri - mobil için daha yavaş yapıldı
      camera.position.y += sin((time*0.003)+10, 0.003);
      camera.position.x += sin((time*0.002)+5, 0.003);
      camera.position.z += sin((time*0.003), 0.003);
      camera.rotation.z += sin((time*0.002)+15, 0.001);
      lookAt = new THREE.Vector3(0,0,0);
      
      // Renk değişimi - sadece mavi tonları için sınırlandırılmış
      if (Math.abs(color.r - color.rt) >= 5) {
        color.r += color.rs;
      }
      if (Math.abs(color.g - color.gt) >= 5) {
        color.g += color.gs;
      }
      if (Math.abs(color.b - color.bt) >= 5) {
        color.b += color.bs;
      }
      
      if (Math.abs(color.r - color.rt) < 5 &&
          Math.abs(color.g - color.gt) < 5 &&
          Math.abs(color.b - color.bt) < 5) {
        
        // DigiMineX teması için sadece mavi ve yeşil tonlarına odaklan
        color.rt = randint(0, 100); // Daha az kırmızı
        color.gt = randint(50, 150); // Orta seviye yeşil
        color.bt = randint(150, 255); // Yüksek mavi
        
        var divisor = 30; // Daha yavaş renk değişimi
        
        if (color.rt > color.r) {
          color.rs = randint(5, 25) / divisor;
        } else {
          color.rs = -randint(5, 25) / divisor;
        }
        if (color.gt > color.g) {
          color.gs = randint(5, 25) / divisor;
        } else {
          color.gs = -randint(5, 25) / divisor;
        }
        if (color.bt > color.b) {
          color.bs = randint(5, 25) / divisor;
        } else {
          color.bs = -randint(5, 25) / divisor;
        }
      }
      
      var r = Math.round(color.r);
      var g = Math.round(color.g);
      var b = Math.round(color.b);
      
      // Küp hareketleri
      for (var i = 0; i < cubes.length; i++) {
        var cube = cubes[i];
        cube.mesh.position.y = sin((time/100)+cube.aniOffset, cube.height);
        if (cube.colored) {
          cube.mesh.material.color.setHex(rgb2color(r, g, b));
        }
      }
    }
    
    // Animasyon döngüsü
    function render() {
      requestAnimationFrame(render);
      mainloop();
      renderer.render(scene, camera);
    }
    
    render();
    
    // Pencere boyutu değiştiğinde canvas'ı ayarla
    window.addEventListener('resize', onWindowResize, false);
    
    function onWindowResize() {
      camera.aspect = window.innerWidth / window.innerHeight;
      camera.updateProjectionMatrix();
      renderer.setSize(window.innerWidth, window.innerHeight);
    }
});