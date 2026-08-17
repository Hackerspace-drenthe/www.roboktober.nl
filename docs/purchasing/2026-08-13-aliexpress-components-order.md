# AliExpress Components Order - 2026-08-13

## Status
- Order status: In verwerking
- Order date: 2026-08-13
- Purpose: Components for antweight controller and robot builds
- Battery policy: only safe 2S LiPo packs are allowed for this project.

## Financial Summary
- Number of orders: 9
- Total spend: EUR 327.15
- Delay coupon note: Multiple orders mention "Coupon voor EUR 1 bij vertraging"
- Return policy note: All listed as "Gratis retourneren"

## Quantity Summary (Practical)
- N20 gear motors (6V, 500 RPM): 60 pcs
- DRV8833 motor driver modules: 50 pcs
- JST XH 2.54 male 2P connector sets: 100 pcs (50 x 2)
- 40-pin breakable male headers: 90 strips (30 x 3)
- ESP32-C3 SuperMini boards: 40 pcs (20 + 20)
- Buck converters 5-40V to 3.3V (variant A): 3 pcs
- Buck converters DD4012SA 5-40V to 3.3V (variant B): 40 pcs (10 x 4)
- SPDT slide switches (3-pin): 40 pcs (20 x 2)

## Detailed Order List

| Ref number | Store | Item | Variant | Qty | Order total | Item link | Order details |
| --- | --- | --- | --- | ---: | ---: | --- | --- |
| 3076007912947964 | Glintdeer Store | N20 Mini Micro Metal Gear Motor | 500 RPM, 6V | 60 | EUR 120.68 | [Product](https://www.aliexpress.com/item/33022320164.html) | [Details](https://www.aliexpress.com/p/order/detail.html?orderId=3076007912947964) |
| 3075448340947964 | Shop1104003965 Store | DRV8833 Motor Drive Module | 50pcs pack | 1 pack | EUR 35.82 | [Product](https://www.aliexpress.com/item/1005009044264044.html) | [Details](https://www.aliexpress.com/p/order/detail.html?orderId=3075448340947964) |
| 3075575246397964 | YF Connector Store | JST XH2.54 connector set | Male, 2P, 50 pcs | 2 packs | EUR 6.78 | [Product](https://www.aliexpress.com/item/1005007384077724.html) | [Details](https://www.aliexpress.com/p/order/detail.html?orderId=3075575246397964) |
| 3075575246337964 | FuXing Store | 40-pin breakable male headers | 30 strips | 3 packs | EUR 12.43 | [Product](https://www.aliexpress.com/item/1005007039504981.html) | [Details](https://www.aliexpress.com/p/order/detail.html?orderId=3075575246337964) |
| 3075575246357964 | Estardyn Official Store | ESP32-C3 SuperMini board | ESP32-C3 SuperMini | 20 | EUR 40.97 | [Product](https://www.aliexpress.com/item/1005007446721319.html) | [Details](https://www.aliexpress.com/p/order/detail.html?orderId=3075575246357964) |
| 3075575246377964 | Estardyn Choice Store | ESP32-C3 SuperMini board | ESP32-C3 MINI | 20 | EUR 63.43 | [Product](https://www.aliexpress.com/item/1005006170575141.html) | [Details](https://www.aliexpress.com/p/order/detail.html?orderId=3075575246377964) |
| 3076006398587964 | MWSS PCB Store | DC-DC Buck converter module | 3.3V | 3 | EUR 11.92 | [Product](https://www.aliexpress.com/item/1005003574828248.html) | [Details](https://www.aliexpress.com/p/order/detail.html?orderId=3076006398587964) |
| 3076006398607964 | Shop1100369141 Store | SPDT slide switch, 3-pin | 20pcs pack | 2 packs | EUR 8.33 | [Product](https://www.aliexpress.com/item/1005009344796225.html) | [Details](https://www.aliexpress.com/p/order/detail.html?orderId=3076006398607964) |
| 3076006398647964 | SZHJW Technology Store | DD4012SA Buck converter module | 3.3V, 10pcs | 4 packs | EUR 26.79 | [Product](https://www.aliexpress.com/item/1005009774585983.html) | [Details](https://www.aliexpress.com/p/order/detail.html?orderId=3076006398647964) |

## Receiving Checklist
- Verify per-order quantities against this document and AliExpress details page.
- For ESP32-C3 boards, separate by supplier/batch (Estardyn Official vs Estardyn Choice).
- For DRV8833 and buck modules, do a quick random sample power-up test before full stock intake.
- For N20 motors, sample-test RPM/current at expected supply levels.
- Label storage bins with order ref number for traceability.

## Notes for Build Planning
- The ESP32-C3 quantity (40) and DRV8833 quantity (50) support multiple build/test batches.
- There are two different 3.3V buck converter product lines; keep them separated until validated.
- Motor quantity (60) is sufficient for robot builds plus spares and destructive testing.

## 2S Battery Safety Requirement
- Only use 2S LiPo packs (7.4 V nominal, 8.4 V fully charged).
- Do not use 1S packs for this controller platform.
- Prefer protected packs or use an external low-voltage cutoff in firmware/hardware.
- Require balance charging (2S mode) and storage-charge handling.
- On intake, reject swollen, damaged, or unlabeled packs.
