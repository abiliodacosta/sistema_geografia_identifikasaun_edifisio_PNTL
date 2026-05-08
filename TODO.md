# TODO: Make Mensajen Reply Page Beautiful & Fix Modal Issues

## Status: [x] Phase 1 Step 1 - Modal investigation complete (Bootstrap loaded, no obvious errors). Ready for JS fix.

### Phase 1: Fix & Core Improvements [x]
- [x] 1. Fix modal opening ✅ (JS init, z-index, styles)
- [x] 2. Add search/filter bar ✅ (live JS filter + status dropdown)
- [x] 3. Unread AJAX refresh ✅
- [x] 4. Table sorting/export ✅

### Phase 2: DB & Reply History [ ]
- [ ] 5. Create tb_replies table via SQL command
- [ ] 6. ALTER tb_mensajen ADD reply_count INT DEFAULT 0
- [ ] 7. Update reply logic to save to tb_replies + increment count

### Phase 3: UI/UX Polish [ ]
- [ ] 8. Glassmorphism modals + animations (CSS updates)
- [ ] 9. Reply templates dropdown (Thank you, In progress, etc.)
- [ ] 10. Toasts for success/error (no page reload)
- [ ] 11. Threaded reply preview in modal
- [ ] 12. Bulk select actions (mark read/delete)
- [ ] 13. Empty state illustration

### Phase 4: JS & Responsiveness [ ]
- [ ] 14. Create admin/js/mensajen.js for AJAX
- [ ] 15. Mobile swipe actions
- [ ] 16. Auto-focus reply textarea

### Phase 5: Languages & Test [ ]
- [ ] 17. Update lang JSONs with new keys
- [ ] 18. Test all langs (tet/pt/en)
- [ ] 19. Test email sending + history

**Next Step: 1. [x] Modal JS fix applied to view.php**

