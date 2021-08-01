<?php
use App\Http\Controllers\EntrevistaProgramadaController;

Route::get('entrevistas_programadas', [EntrevistaProgramadaController::class,'index'])->name('entrevistas_programadas.index')->middleware('sesion.iniciada');
Route::get('entrevistas_programadas/agregar', [EntrevistaProgramadaController::class,'agregar'])->name('entrevistas_programadas.agregar')->middleware('sesion.iniciada');
Route::get('entrevistas_programadas/{id}', [EntrevistaProgramadaController::class,'mostrar'])->name('entrevistas_programadas.mostrar')->middleware('sesion.iniciada');
Route::post('entrevistas_programadas', [EntrevistaProgramadaController::class,'insertar'])->name('entrevistas_programadas.insertar')->middleware('sesion.iniciada');
Route::get('entrevistas_programadas/{id}/modificar', [EntrevistaProgramadaController::class,'modificar'])->name('entrevistas_programadas.modificar')->middleware('sesion.iniciada');
Route::put('entrevistas_programadas/{entrevista_programada}', [EntrevistaProgramadaController::class,'actualizar'])->name('entrevistas_programadas.actualizar')->middleware('sesion.iniciada');
Route::delete('entrevistas_programadas/{entrevista_programada}', [EntrevistaProgramadaController::class,'eliminar'])->name('entrevistas_programadas.eliminar')->middleware('sesion.iniciada');
