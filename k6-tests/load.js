import http from 'k6/http';
import { check, sleep } from 'k6';

const BASE = 'https://admin.blackbook-forgraduation.com/api/v1';
const HEADERS = { 'Accept': 'application/json' };

// open() must be in the init stage (global scope)
const imgData = open('./sample.jpg', 'b');

export const options = {
  stages: [
    { duration: '1m', target: 10 },
    { duration: '3m', target: 20 },
    { duration: '1m', target: 0  },
  ],
  thresholds: {
    http_req_failed:   ['rate<0.01'],
    http_req_duration: ['p(95)<2000'],
  },
};

export default function () {

  // ── 1. App startup calls ──────────────────────────────────────
  check(http.get(`${BASE}/book_type`, { headers: HEADERS }),
    { 'book_type 200': r => r.status === 200 });

  check(http.get(`${BASE}/book_design`, { headers: HEADERS }),
    { 'book_design 200': r => r.status === 200 });

  check(http.get(`${BASE}/book_design_categories`, { headers: HEADERS }),
    { 'categories 200': r => r.status === 200 });

  check(http.get(`${BASE}/svg-categories`, { headers: HEADERS }),
    { 'svg-categories 200': r => r.status === 200 });

  check(http.get(`${BASE}/book_decorations`, { headers: HEADERS }),
    { 'decorations 200': r => r.status === 200 });

  sleep(1);

  // ── 2. User selects university / diploma ──────────────────────
  check(http.get(`${BASE}/universities`, { headers: HEADERS }),
    { 'universities 200': r => r.status === 200 });

  check(http.get(`${BASE}/universities/10/majors`, { headers: HEADERS }),
    { 'majors 200': r => r.status === 200 });

  check(http.get(`${BASE}/diplomas`, { headers: HEADERS }),
    { 'diplomas 200': r => r.status === 200 });

  sleep(1);

  // ── 3. Delivery location ──────────────────────────────────────
  check(http.get(`${BASE}/locations/governorates`, { headers: HEADERS }),
    { 'governorates 200': r => r.status === 200 });

  check(http.get(`${BASE}/locations/cities/1`, { headers: HEADERS }),
    { 'cities 200': r => r.status === 200 });

  check(http.get(`${BASE}/locations/areas/18`, { headers: HEADERS }),
    { 'areas 200': r => r.status === 200 });

  sleep(1);

  // ── 4. Upload front image ─────────────────────────────────────
  const uploadRes = http.post(
    `${BASE}/user_upload_image`,
    { image: http.file(imgData, 'sample.jpg', 'image/jpeg') },
    { headers: HEADERS }
  );
  check(uploadRes, { 'upload 200': r => r.status === 200 });

  const imageId = uploadRes.json('data.image_id');
  sleep(1);

  // ── 5. Submit order ───────────────────────────────────────────
  const order = http.post(`${BASE}/orders`, JSON.stringify({
    user_gender: 'male',
    book_type_id: 5,
    book_design_id: 40,
    front_image_id: imageId || null,
    user_type: 'university',
    university_id: 10,
    university_major_id: 1115,
    username_ar: 'اختبار تحميل',
    username_en: 'Load Test',
    user_phone_number: '0501234567',
    is_sponge: false,
    pages_number: 100,
    delivery_number_one: '0501234567',
    delivery_target: 'home',
    governorate_id: 1,
    city_id: 18,
    area_id: 1,
    gift_type: 'none',
    back_image_ids: [],
    additional_image_id: [],
    custom_design_image_id: [],
  }), {
    headers: Object.assign({}, HEADERS, { 'Content-Type': 'application/json' }),
  });

  check(order, {
    'order 201': r => r.status === 201,
    'no 500':    r => r.status !== 500,
  });

  sleep(2);
}
