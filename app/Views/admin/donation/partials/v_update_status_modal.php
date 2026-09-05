<template x-teleport="body">

    <div
        x-data="updateStatusModal()"
        x-show="open"
        x-cloak
        id="modal-update-status"
        class="fixed inset-0
               flex items-center justify-center
               bg-black/50 backdrop-blur-sm p-4"

        style="z-index: 2147483647;"
        x-transition.opacity
    >

        <div
            @click.away="close()"
            class="bg-white rounded-3xl shadow-2xl w-full max-w-xl max-h-[90vh] flex flex-col overflow-hidden border border-gray-100"
            x-transition
        >

            <!-- HEADER -->
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 shrink-0">

                <div>

                    <h4 class="font-bold text-gray-800 text-base">
                        Update Status Donatur
                    </h4>

                    <p class="text-xs text-gray-500 mt-1">
                        Donatur:
                        <span
                            class="font-semibold text-indigo-600"
                            x-text="updateName"
                        ></span>
                    </p>

                </div>

                <button
                    @click="close()"
                    type="button"
                    class="p-2 bg-gray-100 text-gray-500 hover:bg-gray-200 rounded-xl transition-all"
                >
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>

            </div>


            <!-- FORM -->
            <form
                @submit.prevent="submit"
                class="flex flex-col min-h-0"
            >

                <div class="overflow-y-auto px-6 py-5 space-y-5">

                    <!-- STATUS -->
                    <div>

                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase">
                            Pilih Status Baru
                        </label>

                        <select
                            x-model.number="form.id_status_donasi"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 text-xs outline-none bg-gray-50 focus:ring-2 focus:ring-blue-500"
                            required
                        >

                            <option value="">
                                -- Pilih Status Tujuan --
                            </option>

                            <template
                                x-for="st in allowedStatuses"
                                :key="st.id_status_donasi"
                            >

                                <option
                                    :value="st.id_status_donasi"
                                    x-text="st.status_donasi"
                                ></option>

                            </template>

                        </select>

                    </div>


                    <!-- WAKTU -->
                    <div>

                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase">
                            Waktu Proses
                        </label>

                        <input
                            type="datetime-local"
                            x-model="form.waktu"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 text-xs outline-none bg-gray-50 focus:ring-2 focus:ring-blue-500"
                        >

                        <p class="text-[10px] text-gray-400 mt-1">
                            Dikosongkan akan menggunakan waktu saat ini.
                        </p>

                    </div>


                    <!-- PENGURUS -->
                    <div>

                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase">
                            Nama Pengurus
                        </label>

                        <select
                            id="select-pengurus"
                            class="w-full select2-dynamic"
                        ></select>

                        <p class="text-[10px] text-gray-400 mt-1">
                            Bisa dipilih dari riwayat atau diketik nama baru.
                        </p>

                    </div>

                </div>


                <!-- FOOTER -->
                <div
                    class="px-6 py-4 border-t border-gray-100 bg-white shrink-0 flex justify-end gap-3"
                >

                    <button
                        @click="close()"
                        type="button"
                        class="px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl text-xs font-bold hover:bg-gray-200 transition-all"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-lg shadow-blue-100 transition-all flex items-center justify-center gap-3 active:scale-95"
                    >
                        Simpan Perubahan
                    </button>

                </div>

            </form>

        </div>

    </div>
</template>
