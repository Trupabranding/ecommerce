import { Product } from './types';

export const PRODUCTS: Product[] = [
  {
    id: 'archival-trench-coat',
    name: 'The Archival Trench Coat',
    price: 420,
    description: 'A structured, double-breasted trench crafted from water-repellent organic cotton twill. Featuring deep storm flaps, adjustable wrist straps, and a clean, removable waist belt for a structured or relaxed drape. An essential outerwear statement designed to age gracefully.',
    images: [
      'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?q=80&w=600&auto=format&fit=crop',
      'https://images.unsplash.com/photo-1548624149-f9b1859aa7d0?q=80&w=600&auto=format&fit=crop'
    ],
    category: 'Outerwear',
    sizes: ['XS', 'S', 'M', 'L', 'XL'],
    colors: [
      { name: 'Oatmeal', value: '#E3D7C5' },
      { name: 'Noir', value: '#1C1C1C' },
      { name: 'Sage', value: '#9A9F8C' }
    ],
    features: [
      'Water-repellent organic cotton twill construction',
      'Double-breasted front with real horn buttons',
      'Removable waist belt with leather-wrapped buckle',
      'Deep storm flap back venting'
    ],
    details: [
      { label: 'Material', value: '100% Organic Cotton' },
      { label: 'Origin', value: 'Ethically made in Portugal' },
      { label: 'Care', value: 'Dry clean only' },
      { label: 'Fit', value: 'Slightly oversized drape. Model is 5\'10" wearing S' }
    ],
    rating: 4.9,
    reviewsCount: 38,
    inStock: true,
    featured: true
  },
  {
    id: 'studio-pleated-trouser',
    name: 'The Studio Pleated Trouser',
    price: 260,
    description: 'Tailored with a high-rise waist and double-forward pleats, these trousers are cut from a heavyweight wool-blend drape. They fall into a relaxed, wide-leg silhouette that stacks perfectly over boots, loafers, or minimal sneakers.',
    images: [
      'https://images.unsplash.com/photo-1509551388413-e18d0ac5d495?q=80&w=600&auto=format&fit=crop',
      'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?q=80&w=600&auto=format&fit=crop'
    ],
    category: 'Trousers',
    sizes: ['24', '26', '28', '30', '32'],
    colors: [
      { name: 'Noir', value: '#1C1C1C' },
      { name: 'Charcoal', value: '#4A4A4A' },
      { name: 'Sand', value: '#D2C4B1' }
    ],
    features: [
      'Heavyweight flowing wool-viscose blend drape',
      'High-waisted fit with double pleat front detail',
      'Slanted side pockets and rear welt pockets',
      'Unfinished hem for personalized length tailoring'
    ],
    details: [
      { label: 'Material', value: '55% Wool, 45% Viscose' },
      { label: 'Origin', value: 'Sourced and crafted in Italy' },
      { label: 'Care', value: 'Dry clean recommended' },
      { label: 'Fit', value: 'True to waist size, wide leg' }
    ],
    rating: 4.7,
    reviewsCount: 52,
    inStock: true,
    featured: true
  },
  {
    id: 'heavyweight-ribbed-knit',
    name: 'The Heavyweight Ribbed Knit',
    price: 195,
    description: 'A sculptural chunky sweater knitted from premium Italian extra-fine merino wool and organic cotton. Designed with dramatic drop shoulders, an oversized mock-neck collar, and wide ribbed hem for a striking silhouette.',
    images: [
      'https://images.unsplash.com/photo-1620799140408-edc6dcb6d633?q=80&w=600&auto=format&fit=crop',
      'https://images.unsplash.com/photo-1574169208507-84376144848b?q=80&w=600&auto=format&fit=crop'
    ],
    category: 'Knitwear',
    sizes: ['S', 'M', 'L', 'XL'],
    colors: [
      { name: 'Ivory', value: '#F3EFE9' },
      { name: 'Camel', value: '#C19A6B' },
      { name: 'Noir', value: '#1C1C1C' }
    ],
    features: [
      'Knitted from extra-fine Italian merino-cotton blend',
      'Oversized ribbing details on cuffs, collar, and hem',
      'High mock neck structure that retains its shape',
      'Comfortable cozy heavyweight feel'
    ],
    details: [
      { label: 'Material', value: '70% Merino Wool, 30% Organic Cotton' },
      { label: 'Origin', value: 'Sourced in Italy, knitted in Romania' },
      { label: 'Care', value: 'Hand wash cold, lay flat to dry' },
      { label: 'Fit', value: 'Oversized slouchy fit. Size down for regular fit' }
    ],
    rating: 4.8,
    reviewsCount: 64,
    inStock: true,
    featured: true
  },
  {
    id: 'raw-edge-denim-jacket',
    name: 'The Raw Edge Denim Jacket',
    price: 310,
    description: 'An oversized utility jacket made from 14oz Japanese selvedge denim. Finished with contrasting matte steel buttons, deep front patch pockets, and a raw, deconstructed hemline that frays gently over time for a personalized character.',
    images: [
      'https://images.unsplash.com/photo-1576995853123-5a10305d93c0?q=80&w=600&auto=format&fit=crop',
      'https://images.unsplash.com/photo-1611312449412-6cefac5dc3e4?q=80&w=600&auto=format&fit=crop'
    ],
    category: 'Outerwear',
    sizes: ['XS', 'S', 'M', 'L'],
    colors: [
      { name: 'Vintage Indigo', value: '#5D7C94' },
      { name: 'Ecru', value: '#F2F0EA' }
    ],
    features: [
      '14oz authentic Japanese selvedge denim',
      'Raw, unwashed deconstructed fringe hem',
      'Branded matte-steel shank closures',
      'Dual flat chest pockets and side pockets'
    ],
    details: [
      { label: 'Material', value: '100% Selvedge Cotton Denim' },
      { label: 'Origin', value: 'Made in Kojima, Okayama (Japan)' },
      { label: 'Care', value: 'Wash cold inside out, hang dry' },
      { label: 'Fit', value: 'Boxy, cropped fit. Sized unisex' }
    ],
    rating: 4.6,
    reviewsCount: 29,
    inStock: true,
    featured: false
  },
  {
    id: 'minimalist-carryall-tote',
    name: 'The Minimalist Carryall Tote',
    price: 340,
    description: 'An unstructured tote bag crafted from rich full-grain Tuscan calfskin. Featuring a spacious, raw suede-lined interior, a secure internal zip pouch for smaller essentials, and elegant hand-painted raw edges.',
    images: [
      'https://images.unsplash.com/photo-1584917865442-de89df76afd3?q=80&w=600&auto=format&fit=crop',
      'https://images.unsplash.com/photo-1547949003-9792a18a2601?q=80&w=600&auto=format&fit=crop'
    ],
    category: 'Accessories',
    sizes: ['One Size'],
    colors: [
      { name: 'Saddle Brown', value: '#8B5A2B' },
      { name: 'Noir', value: '#1C1C1C' },
      { name: 'Espresso', value: '#3E2723' }
    ],
    features: [
      'Tuscan full-grain vegetable-tanned leather',
      'Interior is lined with luxurious soft raw suede',
      'Detachable leather matching wallet pouch',
      'Reinforced hand-stitched leather shoulder straps'
    ],
    details: [
      { label: 'Material', value: '100% Full-Grain Calfskin' },
      { label: 'Origin', value: 'Handcrafted in Florence, Italy' },
      { label: 'Dimensions', value: '14" H x 18" W x 6" D' },
      { label: 'Care', value: 'Treat with leather conditioner' }
    ],
    rating: 4.9,
    reviewsCount: 43,
    inStock: true,
    featured: false
  },
  {
    id: 'column-linen-dress',
    name: 'The Column Linen Dress',
    price: 280,
    description: 'An elegant, floor-skimming dress crafted from breathable midweight Belgian linen. Features a structured high halter neckline, a subtle back slit for easy stride, and an adjustable, invisible side zip closure. Extremely versatile for both formal and casual settings.',
    images: [
      'https://images.unsplash.com/photo-1595777457583-95e059d581b8?q=80&w=600&auto=format&fit=crop',
      'https://images.unsplash.com/photo-1496747611176-843222e1e57c?q=80&w=600&auto=format&fit=crop'
    ],
    category: 'Dresses',
    sizes: ['XS', 'S', 'M', 'L'],
    colors: [
      { name: 'Chalk White', value: '#F9F9F8' },
      { name: 'Noir', value: '#1C1C1C' },
      { name: 'Clay', value: '#B38B7D' }
    ],
    features: [
      'Premium breathable Belgian flax midweight linen',
      'Sleek halter neck cut with double fabric chest lining',
      'Ankle-length column profile',
      'Invisible side zipper and delicate single button neck closure'
    ],
    details: [
      { label: 'Material', value: '100% Pure Flax Linen' },
      { label: 'Origin', value: 'Spun in Belgium, tailored in France' },
      { label: 'Care', value: 'Machine wash delicate cold or dry clean' },
      { label: 'Fit', value: 'Slim column profile. Model is 5\'9" wearing XS' }
    ],
    rating: 4.8,
    reviewsCount: 21,
    inStock: true,
    featured: true
  }
];

export const CATEGORIES = ['All', 'Outerwear', 'Knitwear', 'Trousers', 'Dresses', 'Accessories'];
