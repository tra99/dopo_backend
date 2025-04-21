// mission
export interface IMission {
  id: number;
  start_date: string;
  end_date: string;
  purpose: string;
  description: string;
  status: string;
  created_at: string;
  updated_at: string;
  perspective: string | null;
  conclusion: string | null;
  appendix: string | null;
  report_uri: string | null;
  attendance_uri: string | null;
  assessment_uri: string | null;
  slide_uri: string | null;
  schools: School[];
  participants?: any[]
}
export interface School {
  id: number;
  school_code: string;
  school_name_kh: string;
  school_name_en: string;
  province_en: string;
  province_kh: string;
  school_type_en: string;
  school_type_kh: string;
  pivot: Pivot;
}

interface Pivot {
  mission_id: number;
  school_id: number;
}


// evaluation
export interface IEvaluation {
  id: number;
  evaluation_criteria_id: number;
  school_id: number;
  mission_id: number;
  result: string;
  score: string;
  description: string;
  documents: null;
  created_at: string;
  updated_at: string;
  school: {
    id: number;
    school_code: string;
    school_name_kh: string;
    school_name_en: string;
    school_type_kh: string;
    school_type_en: string;
    sis: string;
    village_kh: string;
    village_en: string;
    commune_kh: string;
    commune_en: string;
    district_kh: string;
    district_en: string;
    province_kh: string;
    province_en: string;
    created_at: string;
    updated_at: string;
  };
  mission: {
    id: number;
    start_date: string;
    end_date: string;
    purpose: string;
    description: string;
    status: string;
    created_at: string;
    updated_at: string;
  };
  evaluation_criteria: {
    id: number;
    title: string;
    options: string;
    question_id: number;
    created_at: string;
    updated_at: string;
  };
}

//school participant
export interface ISchoolParticipant {
  school: School;
  data: {
    id: number;
    school_id: number;
    mission_id: number;
    organization: string;
    number_of_male: number;
    number_of_female: number;
    number_of_total: number;
    file_uri: null;
    created_at: string;
    updated_at: string;
  }[];
}

export interface IChallenge {
  school_id: string;
  mission_id: string;
  category_id: string;
  challenge: string;
  solution: string;
  updated_at: string;
  created_at: string;
  id: number;
};


export interface IChallenge {
  school: School;
  data: {
    id: number;
    school_id: number;
    mission_id: number;
    category_id: number;
    challenge: string;
    solution: string;
    school: School;
    mission: {
      id: number;
      purpose: string;
    };
    category: {
      id: number;
      title: string;
      parent_id: number;
      parent: {
        id: number;
        title: string;
        parent_id: null;
        type: string;
      };
    };
  }[];
}

export interface ICategoryStandard {
  id: number;
  title: string;
  parent_id: null;
  type: string;
  status: string;
  created_at: string;
  updated_at: string;
  school_type_id: number;
  children_count: number;
  questions_count: number;
  children: {
    id: number;
    title: string;
    parent_id: number;
    children: any[];
  }[];
  school_type: {
    id: number;
    school_type_en: string;
    school_type_kh: string;
  };
}

export interface IAchievement {
  id: number;
  support_phase_id: number;
  mission_id: number;
  school_id: number;
  created_at: string;
  updated_at: string;
  support_phase: {
    id: number;
    title: string;
  }
  categories_or_support_phases: {
    id: number;
    achievement_id: number;
    category_id: null;
    support_phase_id: number;
    description: string;
    created_at: null;
    updated_at: null;
    category: null;
    support_phase: {
      id: number;
      number: string;
      title: string;
      parent_id: number;
      status: string;
      school_type_id: number;
      created_at: string;
      updated_at: string;
    };
  }[];
  grouped_categories: {
    [key: string] : {
      id: number;
      title: string;
      parent_id: number;
      type: string;
      status: string;
      created_at: string;
      updated_at: string;
      school_type_id: number;
      pivot: {
        achieve_id: number;
        category_id: number;
      }
      parent: {
        id: number;
        title: string;
      }
      questions: {
        id: number;
        category_id: number;
        question: string;
        description: string;
        question_type: string;
        answer_option: string | null;
        rule: string | null;
        created_at: string;
        updated_at: string;
        publised_at: string | null;
        school_type: string;
        evaluation_criterias: {
          id: number;
          title: string;
          options: string;
          question_id: number;
          created_at: string;
          updated_at: string;
          evaluations: {
            id: number;
            evaluation_criteria_id: number;
            school_id: number;
            mission_id: number;
            result: string;
            score: string;
            description: string;
            documents: null;
            created_at: string;
            updated_at: string;
          }[]
        }[]
      }[]
    }[]
  }
}
