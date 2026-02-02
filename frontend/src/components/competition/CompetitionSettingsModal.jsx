import React from 'react';
import { X, FileText } from 'lucide-react';

const CompetitionSettingsModal = ({
  showCompSettings,
  setShowCompSettings,
  compName,
  setCompName,
  compSlug,
  setCompSlug,
  competition,
  handleUpdateCompetition,
  savingComp
}) => {
  if (!showCompSettings || !competition) return null;

  return (
    <div className="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-black/90 backdrop-blur-md">
      <div className="bg-slate-900 border border-slate-800 w-full max-w-lg rounded-3xl overflow-hidden shadow-2xl">
        <div className="p-6 border-b border-slate-800 flex justify-between items-center bg-slate-900/50">
          <h3 className="text-white font-black uppercase italic tracking-tighter text-lg">Postavke Takmičenja</h3>
          <button onClick={() => setShowCompSettings(false)} className="text-slate-500 hover:text-white"><X size={20} /></button>
        </div>
        
        <div className="p-8 space-y-6">
          <div className="space-y-2">
            <label className="text-[10px] font-black text-slate-500 uppercase tracking-widest px-1">Naziv Takmičenja</label>
            <input 
              type="text" 
              className="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 text-white font-bold outline-none focus:border-blue-500 transition-all"
              value={compName}
              onChange={(e) => setCompName(e.target.value)}
              placeholder="npr. Joola Cup 2024"
            />
          </div>

          <div className="space-y-2">
            <label className="text-[10px] font-black text-slate-500 uppercase tracking-widest px-1">Link (Slug)</label>
            <div className="relative">
              <div className="absolute left-4 top-1/2 -translate-y-1/2 text-slate-600 font-bold text-xs">
                pingpong.ba/p/
              </div>
              <input 
                type="text" 
                className="w-full bg-slate-950 border border-slate-800 rounded-2xl py-4 pl-32 pr-4 text-blue-400 font-bold outline-none focus:border-blue-500 transition-all"
                value={compSlug}
                onChange={(e) => setCompSlug(e.target.value)}
                placeholder="joola-cup"
              />
            </div>
            <p className="text-[9px] text-slate-600 font-bold uppercase tracking-wider px-1">
              * Koristi se za javni link. Dozvoljena samo mala slova, brojevi i crtice.
            </p>
          </div>

          {competition.slug && (
            <div className="bg-blue-600/5 border border-blue-500/10 rounded-2xl p-4 flex items-center justify-between">
              <div className="flex items-center gap-3">
                <FileText size={16} className="text-blue-500" />
                <div>
                  <p className="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Javni Link</p>
                  <p className="text-xs text-white font-bold">pingpong.ba/p/{competition.slug}</p>
                </div>
              </div>
              <button 
                onClick={() => {
                    navigator.clipboard.writeText(`https://pingpong.ba/p/${competition.slug}`);
                    alert("Link kopiran!");
                }}
                className="text-blue-500 hover:text-blue-400 font-black uppercase text-[10px]"
              >
                Kopiraj
              </button>
            </div>
          )}

          <div className="pt-4 flex gap-3">
            <button 
              onClick={() => setShowCompSettings(false)}
              className="flex-1 bg-slate-800 text-white py-4 rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-slate-700 transition-all"
            >
              Otkaži
            </button>
            <button 
              onClick={handleUpdateCompetition}
              disabled={savingComp}
              className="flex-[2] bg-blue-600 text-white py-4 rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-blue-500 transition-all shadow-xl shadow-blue-500/20 disabled:opacity-50"
            >
              {savingComp ? 'Spremanje...' : 'Sačuvaj Promjene'}
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};

export default CompetitionSettingsModal;
