export interface ColorOption {
  name: string;
  value: string;
}

export interface ProductDetail {
  label: string;
  value: string;
}

export interface Product {
  id: string;
  name: string;
  price: number;
  description: string;
  images: string[];
  category: string;
  sizes: string[];
  colors: ColorOption[];
  features: string[];
  details: ProductDetail[];
  rating: number;
  reviewsCount: number;
  inStock: boolean;
  featured?: boolean;
}

export interface CartItem {
  id: string; // unique cart item id (product.id + size + color)
  product: Product;
  selectedSize: string;
  selectedColor: ColorOption;
  quantity: number;
}

export interface ShippingAddress {
  email: string;
  firstName: string;
  lastName: string;
  address: string;
  apartment: string;
  city: string;
  state: string;
  country: string;
  postalCode: string;
  phone: string;
}

export interface PaymentDetails {
  cardNumber: string;
  cardName: string;
  expiry: string;
  cvc: string;
}

export interface Order {
  id: string;
  items: CartItem[];
  subtotal: number;
  shippingCost: number;
  tax: number;
  total: number;
  shippingAddress: ShippingAddress;
  paymentMethod: string;
  createdAt: string;
}
