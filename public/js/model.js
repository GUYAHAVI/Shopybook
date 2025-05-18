// import * as THREE from 'https://cdn.skypack.dev/three@0.129.0/build/three.module.js';
// import { GLTFLoader } from 'https://cdn.skypack.dev/three@0.129.0/examples/jsm/loaders/GLTFLoader.js';
// import { gsap } from 'https://cdn.skypack.dev/gsap';

// const camera = new THREE.PerspectiveCamera(
//     75,
//     window.innerWidth / window.innerHeight,
//     0.1,
//     1000
// );
// camera.position.z = 4;

// const scene = new THREE.Scene();
// let bee;
// let mixer;
// const loader = new GLTFLoader();
// loader.load('../models/giraffe\ .glb',
//     function (gltf) {
//         bee = gltf.scene;

//         bee.traverse((child) => {
//             if (child.isMesh) {
//                 // Clone the existing material to maintain texture and lighting properties
//                 const material = child.material.clone();
//                 material.transparent = true;

//                 const positionAttribute = child.geometry.attributes.position;
//                 const opacityArray = new Float32Array(positionAttribute.count);

//                 // Set opacity based on Y-coordinate of each vertex
//                 for (let i = 0; i < positionAttribute.count; i++) {
//                     const y = positionAttribute.getY(i);  // Get Y-coordinate
//                     const opacity = THREE.MathUtils.clamp((y + 2) / 4, 0.5, 0); // Adjust transparency range
//                     opacityArray[i] = opacity;
//                 }

//                 // Create a new buffer attribute for opacity and add it to the geometry
//                 child.geometry.setAttribute('opacity', new THREE.BufferAttribute(opacityArray, 1));

//                 // Modify the shader to apply per-vertex opacity
//                 material.onBeforeCompile = (shader) => {
//                     shader.vertexShader = `
//                         attribute float opacity;
//                         varying float vOpacity;
//                         ${shader.vertexShader}
//                     `.replace(
//                         `#include <begin_vertex>`,
//                         `
//                             #include <begin_vertex>
//                             vOpacity = opacity;
//                         `
//                     );

//                     shader.fragmentShader = `
//                         varying float vOpacity;
//                         ${shader.fragmentShader}
//                     `.replace(
//                         `#include <dithering_fragment>`,
//                         `
//                             gl_FragColor.a *= vOpacity;
//                             #include <dithering_fragment>
//                         `
//                     );
//                 };

//                 child.material = material; // Apply the modified material
//             }
//         });

//         scene.add(bee);

//         mixer = new THREE.AnimationMixer(bee);
//         mixer.clipAction(gltf.animations[0]).play();
//         modelMove();
//     },
//     function (xhr) {},
//     function (error) {}
// );

// const renderer = new THREE.WebGLRenderer({alpha: true});
// renderer.setSize(window.innerWidth, window.innerHeight);
// document.getElementById('container3D').appendChild(renderer.domElement);

// // light
// const ambientLight = new THREE.AmbientLight(0xffffff, 1.3);
// scene.add(ambientLight);

// const topLight = new THREE.DirectionalLight(0xffffff, 1);
// topLight.position.set(500, 500, 500);
// scene.add(topLight);

// const reRender3D = () => {
//     requestAnimationFrame(reRender3D);
//     renderer.render(scene, camera);
//     if(mixer) mixer.update(0.02);
// };
// reRender3D();

// // mouse movement interaction
// let mouseX = 0;
// let mouseY = 0;
// const rotationFactor = 0.005;  // Control the rotation sensitivity
// const positionFactor = 0.01;   // Control how much the model moves towards the pointer

// document.addEventListener('mousemove', (event) => {
//     mouseX = (event.clientX / window.innerWidth) * 2 - 1; // Normalize to range [-1, 1]
//     mouseY = (event.clientY / window.innerHeight) * 2 - 1;

//     if (bee) {
//         // Rotate the model in the opposite direction of mouse X movement
//         gsap.to(bee.rotation, {
//             y: -mouseX * Math.PI * 0.5, // Invert rotation (negative value for opposite rotation)
//             duration: 0.5,
//             ease: 'power1.out'
//         });

//         // Move the model closer or farther based on the mouse Y movement
//         gsap.to(bee.position, {
//             x: mouseX * positionFactor * 300, // Moves towards the pointer horizontally
//             duration: 0.5,
//             ease: 'power1.out'
//         });
//     }
// });

// let arrPositionModel = [
//     {
//         id: 'banner',
//         position: {x: 0, y: -1, z: 0},
//         rotation: {x: 0, y: 1.5, z: 0}
//     },
//     {
//         id: "intro",
//         position: { x: 1, y: -1, z: -5 },
//         rotation: { x: 0.5, y: -0.5, z: 0 },
//     },
//     {
//         id: "description",
//         position: { x: -1, y: -1, z: -5 },
//         rotation: { x: 0, y: 0.5, z: 0 },
//     },
//     {
//         id: "contact",
//         position: { x: 0.8, y: -1, z: 0 },
//         rotation: { x: 0.3, y: -0.5, z: 0 },
//     },
// ];
// const modelMove = () => {
//     const sections = document.querySelectorAll('.section');
//     let currentSection;
//     sections.forEach((section) => {
//         const rect = section.getBoundingClientRect();
//         if (rect.top <= window.innerHeight / 3) {
//             currentSection = section.id;
//         }
//     });
//     let position_active = arrPositionModel.findIndex(
//         (val) => val.id == currentSection
//     );
//     if (position_active >= 0) {
//         let new_coordinates = arrPositionModel[position_active];
//         gsap.to(bee.position, {
//             x: new_coordinates.position.x,
//             y: new_coordinates.position.y,
//             z: new_coordinates.position.z,
//             duration: 3,
//             ease: "power1.out"
//         });
//         gsap.to(bee.rotation, {
//             x: new_coordinates.rotation.x,
//             y: new_coordinates.rotation.y,
//             z: new_coordinates.rotation.z,
//             duration: 3,
//             ease: "power1.out"
//         });
//     }
// };

// window.addEventListener('scroll', () => {
//     if (bee) {
//         modelMove();
//     }
// });
// window.addEventListener('resize', () => {
//     renderer.setSize(window.innerWidth, window.innerHeight);
//     camera.aspect = window.innerWidth / window.innerHeight;
//     camera.updateProjectionMatrix();
// });
